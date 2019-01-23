<template>
  <v-container>
    <v-layout>
      <v-flex>
        <h2 class="headline primary--text pb-3">Доступность порталов</h2>
        <Datepicker :dateFrom="from" :dateTo="to" />

        <v-layout row justify-space-between>
          <v-flex xs6>
            <v-btn
              color="deep-purple lighten-1"
              class="white--text mb-4 ml-0"
              :disabled="loading"
              @click="getData"
            >
              <v-icon left dark>refresh</v-icon>
              <span>Обновить</span>
            </v-btn>
          </v-flex>

          <v-flex xs6 class="text-xs-right">
            <v-tooltip top>
              <v-btn
                slot="activator"
                color="deep-purple lighten-2"
                class="white--text mb-4 mr-0"
                :href="xlsExport"
              >
                <v-icon left dark>save_alt</v-icon>
                xlsx
              </v-btn>
              <span>Выгрузить в XLSX</span>
            </v-tooltip>
          </v-flex>
        </v-layout>

        <v-tabs
          dark
          color="blue darken-3"
          show-arrows
          v-if="months"
          :value="tabModel"
        >
          <v-tabs-slider></v-tabs-slider>

          <v-tab
            v-for="(month, i) in months"
            :href="'#tab-' + i"
            :key="`tab-${i}`"
            @click="setMonth(month)"
          >
            {{ month | namedMonth }}
          </v-tab>

        </v-tabs>

        <template v-if="tableData">
          <v-card>

            <v-card-title>
              Доступность по дням
              <v-spacer></v-spacer>
              <v-text-field
                v-model="search"
                append-icon="search"
                label="Поиск по названию портала"
                clearable
                single-line
                hide-details
              >
              </v-text-field>
            </v-card-title>

            <v-data-table
              :headers="tableHeaders"
              :items="filteredRows"
              :loading="loading"
              :custom-sort="customSort"
              disable-initial-sort
            >
              <v-progress-linear
                slot="progress"
                color="blue"
                indeterminate>
              </v-progress-linear>

              <template slot="items" slot-scope="props">
                <td v-for="(item, index) in props.item">
                  <template v-if="index === 0">
                    <span v-html="item.text"></span>
                  </template>
                  <template v-else>
                    <v-icon v-if="item.text === 0" class="green--text pr-2">done</v-icon>
                    <v-icon v-if="item.text === 1" class="orange--text pr-2">error_outline</v-icon>
                    <v-icon v-if="item.text === 2" class="red--text pr-2">close</v-icon>
                  </template>
                </td>
              </template>
            </v-data-table>
          </v-card>
        </template>

      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  import mockTableData from '../../../test/mocks/tableData'
  import Datepicker from '@/components/Controls/Datepicker'
  const config = require('../../../config/reports')

  export default {
    name: 'Portals',
    components: {
      Datepicker
    },
    data() {
      return {
        from: null,
        to: null,
        search: null,
        lastMonthName: null,
        currentMonth: null,
        dev: !!window.webpackHotUpdate
      }
    },
    computed: {
      tableData() {
        return this.dev ? this.getData() : this.$store.getters.portals
      },
      tableHeaders() {
        return this.tableData.months[this.currentMonth].headers
      },
      tableRows() {
        return this.tableData.months[this.currentMonth].rows
      },
      filteredRows() {
        if (this.search) {
          return this.tableRows.filter(row => {
            const link = row[0].text.toLowerCase()
            const search = this.search.toLowerCase().trim()
            return !search || link.includes(search)
          })
        }
        return this.tableRows
      },
      months() {
        if (this.tableData) {
          const months = Object.keys(this.tableData.months)
          this.lastMonthName = months[months.length - 1]
          this.currentMonth = '' || this.lastMonthName
          return months
        }
        return false
      },
      tabModel() {
        return 'tab-' + this.lastMonthIndex
      },
      loading() {
        return this.$store.getters.loading
      },
      lastMonthIndex() {
        return (this.months.length - 1) || 0
      },
      xlsExport() {
        const period = this.$store.getters.period
        return `http://zabbix.fm.epbs.ru/zabbix/reports/api/v1/portals/export/xlsx/?api_token=${config.API_TOKEN}&period=${period}`
      }
    },
    filters: {
      namedMonth(value) {
        const arr = value.split('-')
        const year = arr[0]
        const month = config.MONTHS_NAMES[arr[1]]
        return `${month} ${year}`
      }
    },
    methods: {
      getData() {
        return this.dev
          ? mockTableData
          : this.$store.dispatch('setPortals', {
            from: this.$store.getters.from,
            to: this.$store.getters.to
          })
      },
      setMonth(month) {
        if (this.currentMonth === month) return
        this.currentMonth = month
      },
      customSort(items, index, isDescending) {
        return items.sort((a, b) => {
          if (index === 'name') {
            const a_ = a[0]['text'].replace(/<[^>]*>/g, '').toLowerCase()
            const b_ = b[0]['text'].replace(/<[^>]*>/g, '').toLowerCase()

            if (isDescending) {
              return b_ > a_ ? 1 : -1
            } else {
              return a_ > b_ ? 1 : -1
            }
          }
        })
      }
    },
    created() {
      const today = new Date().toISOString().substr(0, 10)
      const [year, month] = today.split('-')

      const storeFrom = this.$store.getters.from
      const storeTo = this.$store.getters.to

      this.from = storeFrom || `${year}-${month}-01`
      this.to = storeTo || new Date().toISOString().substr(0, 10)

      !this.dev && this.$store.dispatch('setPortals', {
        from: this.from,
        to: this.to
      })
    }
  }
</script>
