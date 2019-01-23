<template>
  <div class="datepicker">
    <p>Выберите период:</p>

    <v-layout row wrap>
      <v-flex xs12 sm6 md4>
        <v-menu
          :close-on-content-click="false"
          v-model="menuFrom"
          :nudge-right="40"
          lazy
          transition="scale-transition"
          offset-y
          full-width
          min-width="290px"
        >
          <v-text-field
            slot="activator"
            v-model="fromFormatted"
            label="От"
            prepend-icon="event"
            readonly
            @blur="from = parseDate(fromFormatted)"
          ></v-text-field>

          <v-date-picker
            v-model="from"
            :max="to"
            @input="menuFrom = false"
            locale="ru-RU"
          >
          </v-date-picker>
        </v-menu>
      </v-flex>

      <v-flex xs12 sm6 md4>
        <v-menu
          :close-on-content-click="false"
          v-model="menuTo"
          :nudge-right="40"
          lazy
          transition="scale-transition"
          offset-y
          full-width
          min-width="290px"
        >
          <v-text-field
            slot="activator"
            v-model="toFormatted"
            label="До"
            prepend-icon="event"
            readonly
            @blur="to = parseDate(toFormatted)"
          ></v-text-field>

          <v-date-picker
            v-model="to"
            :min="from"
            @input="menuTo = false"
            locale="ru-RU"
          >
          </v-date-picker>
        </v-menu>
      </v-flex>

      <v-spacer></v-spacer>
    </v-layout>
  </div>
</template>

<script>
export default {
  name: 'Datepicker',
  props: ['dateFrom', 'dateTo'],
  data() {
    return {
      from: null,
      to: null,
      fromFormatted: this.formatDate(this.from),
      toFormatted: this.formatDate(this.to),
      menuFrom: false,
      menuTo: false
    }
  },
  computed: {
    period() {
      return `${this.formatDate(this.from)}-${this.formatDate(this.to)}`
    }
  },
  watch: {
    from() {
      this.fromFormatted = this.formatDate(this.from)
      this.$store.dispatch('setFrom', this.from).then(_ => {
        this.from = this.$store.getters.from
      })
    },
    to() {
      this.toFormatted = this.formatDate(this.to)
      this.$store.dispatch('setTo', this.to).then(_ => {
        this.to = this.$store.getters.to
      })
    },
    period() {
      this.$store.dispatch('setPeriod', this.period)
    }
  },
  methods: {
    formatDate(date) {
      if (!date) return null
      const [year, month, day] = date.split('-')
      return `${day}.${month}.${year}`
    },
    parseDate(date) {
      if (!date) return null
      const [day, month, year] = date.split('.')
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
    }
  },
  created() {
    this.from = this.dateFrom
    this.to = this.dateTo
  }
}
</script>
