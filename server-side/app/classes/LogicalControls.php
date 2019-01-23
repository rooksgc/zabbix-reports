<?php

namespace ZabbixReports;

/**
 * Работа с данными логических контролей
 */
class LogicalControls {
    /**
     * Экземпляр класса PostgresDb
     *
     * @var Object
     */
    private $postgresDb;

    /**
     * Массив списка файлов для анализа
     *
     * @var Array
     */
    private $filesList;

    /**
     * Дата в формате yyyy-mm-dd
     *
     * @example /usr/share/TestContent/
     * @var Date
     */
    private $date;

    /**
     * Путь к директории с файлами в файловой системе
     * 
     * @example /usr/share/TestContent/
     * @var String
     */
    private $dir;

    /**
     * Строка для фильтрации списка файлов
     * 
     * Функция фильтрации вернет только те файлы,
     * в имени которых найдется совпадение с этой строкой
     * 
     * @example Порталы
     * @var String
     */
    private $filterWord;

    /**
     * Код для поиска в содержимом файла
     * 
     * Используется для выставления статуса аварии
     * 
     * @example WRF-0103
     * @var String
     */
    private $code;

    /**
     * Тип проверяемой сущности
     * 
     * 0 - Портал
     * 1 - Рубрикатор
     * 2 - Веб-сервис
     * 
     * @var Integer
     */
    private $entityType;

    /**
     * Имя таблицы для хранения данных
     * 
     * После обхода списка файлов происходит запись
     * данных в эту таблицу
     * 
     * @example logical_controls
     * @var String
     */
    private $tableName;

    public function __construct($opts) {
        $this->setOpts($opts);
        $this->setFilesList();
        $this->setDate();
    }

    /**
     * Установка параметров
     * 
     * @throws Exception если валидация параметров провалилась
     * @return Void
     */
    private function setOpts($opts) {
        if ( !is_array($opts) || empty($opts)
            || !isset($opts['db'])
            || !( $opts['db'] instanceof \SeinopSys\PostgresDb )
            || !isset($opts['dir'])
            || !isset($opts['filterWord'])
            || !isset($opts['code'])
            || !isset($opts['entityType'])
            || !isset($opts['tableName'])
        ) {
            throw new Exception('Неверные или отсутствующие параметры конструктора класса LogicalControls');
        }

        $this->dir = $opts['dir'];
        $this->filterWord = $opts['filterWord'];
        $this->code = $opts['code'];
        $this->entityType = $opts['entityType'];
        $this->tableName = $opts['tableName'];
        $this->db = $opts['db'];
    }

    /**
     * Обновляет данные в базе для логических контролей
     * 
     * @api /api/v1/logical-controls/update/
     * @throws Exception ошибка в работе с базой данных
     * @return Void
     */
    public function update() {
        $list = $this->filesList;

        // @TODO handle connection errors
        $this->db->getConnection();
        
        foreach ($list as $index => $filename) {
            // Элементы списка
            $entityName = str_replace("_" . $this->date . ".txt", "", $filename);

            $item = array(
                "date" => $this->date,
                "entity_name" => $entityName,
                "entity_type" => $this->entityType,
                "status" => 0
            );

            // Ищем вхождение code
            $file = file($this->dir . $filename);
            $found = false;
            
            foreach ($file as $line) {
                if (stristr($line, $this->code) !== false) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                $item["status"] = 2;
            }

            if (!$this->db->tableExists($this->tableName)) {
                throw new Exception("Таблицы " . $this->tableName . " не существует");
            } 

            $exists = $this->db
                ->where('date', $this->date)
                ->where('entity_name', $entityName)
                ->getOne($this->tableName, 'COUNT(id)');

            // Insert
            if ($exists['count'] == 0) {
                $this->db->insert($this->tableName, $item);
            } else {
                // Update
                $this->db
                    ->where('date', $this->date)
                    ->where('entity_name', $entityName)
                    ->update($this->tableName, ['status' => $item["status"]]);
            }
        }

        if ($this->db->getLastError()) {
            throw new Exception($this->db->getLastError());
        }
    }

    /**
     * Устанавливает свойство filesList
     * Массив имен файлов из каталога dir, содержащих в имени filterWord
     * 
     * @throws Exception
     * @return Void
     */
    private function setFilesList() {
        $list = array_diff( scandir($this->dir), array('..', '.') );

        if ( !is_array($list) || empty($list) ) {
            throw new Exception('Список файлов в директории ' . $this->dir . ' пустой или не может быть получен');
        }

        $this->filesList = $this->filter($list);
    }

    /**
     * Получение даты из имени файла
     * формат yyyy-mm-dd
     * 
     * @throws Exception
     * @return Void
     */
    private function setDate() {
        if (!$this->filesList) {
            throw new Exception('Не удалось получить список файлов в директории ' . $this->dir);
        }

        preg_match('/\d{4}-\d{2}-\d{2}/', $this->filesList[2], $matches);
        $this->date = $matches[0];
    }

    /**
     * Фильтрация списка
     * Возвращает новый массив, элементы которого содержат filterWord
     * 
     * @param Array $arr - массив для фильтрации
     * @return Array $filtered - отфильтрованный массив
     */
    private function filter($list) {
        $filtered = array_filter($list, function($value) {
            return stristr($value, $this->filterWord) !== false;
        });
        
        return $filtered;
    }

}