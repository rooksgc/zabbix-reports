<template>
  <v-container>
    <v-layout>
      <v-flex>
        <h2 class="headline primary--text mb-3">Справка</h2>
        <h4 class="title primary--text mb-3">Статусы</h4>
        <div>
          <p><v-icon class="green--text pr-2">done</v-icon>Штатно. Ресурс работает без инцидентов.</p>
          <p><v-icon class="orange--text pr-2">error_outline</v-icon>Нештатно. Замечены инциденты, ресурс работает нештатно.</p>
          <p><v-icon class="red--text pr-2">close</v-icon>Авария. Ресурс недоступен более одного часа или сообщение о восстановлении еще не приходило.</p>
        </div>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
export default {
  name: 'Reference',
  data() {
    return {
    }
  }
}
</script>
