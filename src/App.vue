<template>
  <v-app id="zabbix-reports">

    <v-navigation-drawer
      :clipped="$vuetify.breakpoint.lgAndUp"
      v-model="drawer"
      width="250"
      fixed
      app
      v-if="isLoggedIn"
    >
      <v-list dised>
        <template v-for="item in items">
          <v-layout
            v-if="item.heading"
            :key="item.heading"
            row
            align-center
          >
            <v-flex xs6>
              <v-subheader v-if="item.heading">
                {{ item.heading }}
              </v-subheader>
            </v-flex>

          </v-layout>

          <v-list-group
            v-else-if="item.children"
            v-model="item.model"
            :key="item.text"
          >
            <v-list-tile slot="activator">
              <v-list-tile-content>
                <v-list-tile-title>
                  {{ item.text }}
                </v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>

            <v-list-tile
              v-for="(child, i) in item.children"
              :key="`child-${i}`"
              :to="child.to"
            >
              <v-list-tile-action v-if="child.icon">
                <v-icon>{{ child.icon }}</v-icon>
              </v-list-tile-action>

              <v-list-tile-content>
                <v-list-tile-title>
                  {{ child.text }}
                </v-list-tile-title>
              </v-list-tile-content>

            </v-list-tile>
          </v-list-group>

          <v-list-tile
            v-else
            :key="item.text"
            :to="item.to"
            @click="">
            <v-list-tile-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>

            <v-list-tile-content>
              <v-list-tile-title>
                {{ item.text }}
              </v-list-tile-title>
            </v-list-tile-content>

          </v-list-tile>

        </template>
      </v-list>
    </v-navigation-drawer>

    <v-toolbar
      :clipped-left="$vuetify.breakpoint.lgAndUp"
      color="blue darken-3"
      dark
      app
      fixed
      v-if="isLoggedIn"
    >
      <v-toolbar-title class="ml-0">
        <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
        <span class="hidden-sm-and-down">Мониторинг доступности сервисов</span>
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <v-icon class="pr-1">date_range</v-icon>
      <div class="pr-2">{{ toolbarDate }}</div>

      <v-btn flat @click="logout">
        Выход
        <v-icon class="pl-2">exit_to_app</v-icon>
      </v-btn>

    </v-toolbar>

    <v-content>
      <router-view></router-view>
    </v-content>

    <template v-if="message">
      <v-snackbar
        :timeout="5000"
        :multi-line="true"
        :bottom="true"
        :right="true"
        :value="true"
        :color="message.type"
        @input="closeMessage"
      >
        {{ message.text }}
        <v-icon
          class="pointer white--text"
          @click.native="closeMessage"
        >close</v-icon>
      </v-snackbar>
    </template>

  </v-app>
</template>

<script>
export default {
  data: () => ({
    dialog: false,
    drawer: null,
    items: [
      {
        icon: 'done_all',
        text: 'Порталы',
        to: '/portals'
      },
      {
        icon: 'toc',
        text: 'Рубрикаторы',
        to: '/rubricators'
      },
      {
        icon: 'web',
        text: 'Веб сервисы',
        to: '/services'
      },
      {
        icon: 'playlist_add_check',
        text: 'Логические контроли',
        to: '/logical-controls'
      },
      {
        icon: 'help',
        text: 'Справка',
        to: '/reference'
      }
    ]
  }),
  computed: {
    message() {
      return this.$store.getters.message
    },
    isLoggedIn() {
      return this.$store.getters.isLoggedIn
    },
    toolbarDate() {
      const d = new Date()
      const day = `${d.getDate()}`.padStart(2, '0')
      const month = `${d.getMonth() + 1}`.padStart(2, '0')
      const year = d.getFullYear()
      return `${day}.${month}.${year}`
    }
  },
  methods: {
    closeMessage() {
      this.$store.dispatch('closeMessage')
    },
    logout() {
      this.$store.dispatch('logout')
      this.$router.push('/')
    }
  },
  created() {
    const uuid = sessionStorage.getItem('zr.auth')

    if (uuid) {
      this.$store.dispatch('autoLogin', uuid)
      // Идем на этот роут если страница перезагрузилась
      this.$router.push('/portals')
    } else {
      this.$router.push('/')
    }
  }
}
</script>

<style scoped>
  .pointer {
    cursor: pointer;
  }
</style>
