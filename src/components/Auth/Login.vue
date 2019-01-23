<template>
  <v-container fluid fill-height>
    <v-layout align-center justify-center>
      <v-flex xs12 sm8 md6>
        <v-card class="elevation-12">
          <v-toolbar dark color="primary">
            <v-toolbar-title>Мониторинг сервисов</v-toolbar-title>
          </v-toolbar>

          <v-card-text @keyup.enter.prevent="login">
            <v-form
              ref="form"
              v-model="valid"
              lazy-validation
              @submit.prevent="login"
            >
              <v-text-field
                id="password"
                prepend-icon="lock"
                name="password"
                label="Пароль"
                type="password"
                :rules="passwordRules"
                v-model="password">
              </v-text-field>

              <v-text-field
                id="captcha"
                prepend-icon="input"
                name="captcha"
                label="Проверочный код"
                type="captcha"
                :rules="captchaRules"
                v-model="captcha">
              </v-text-field>

              <v-img
                :src="captchaImage"
                width="130"
                height="80"
              ></v-img>

              <v-tooltip right>
                <v-icon
                  dark
                  slot="activator"
                  color="primary"
                  class="pointer"
                  @click="updateCaptchaCode"
                >refresh</v-icon>
                <span>Обновить изображение</span>
              </v-tooltip>

            </v-form>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              @click="login"
              :loading="loading"
              :disabled="!valid || loading"
            >Войти</v-btn>
          </v-card-actions>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      password: '',
      captcha: '',
      captchaImage: '',
      captchaCode: '',
      valid: false,
      passwordRules: [
        v => !!v || 'Поле обязательно для заполнения',
        v => (v && v.length >= 9) || 'Поле пароля должно содержать не менее 9 символов'
      ],
      captchaRules: [
        v => !!v || 'Поле обязательно для заполнения'
      ]
    }
  },
  computed: {
    loading() {
      return this.$store.getters.loading
    }
  },
  methods: {
    login() {
      if (this.$refs.form.validate()) {
        const user = {
          password: this.password,
          captcha: this.captcha,
          captchaCode: this.captchaCode
        }
        this.$store.dispatch('login', user)
          .then(() => {
            this.$router.push('/portals')
          })
          .catch(() => {})
      }
    },
    updateCaptchaCode() {
      /** @TODO вынести в api */
      axios.get('http://zabbix.fm.epbs.ru/zabbix/reports/lib/captcha/captcha_api.php')
        .then(res => {
          this.captchaCode = res.data.code
          this.captchaImage = res.data.image
        })
        .catch(err => {
          console.log(err)
        })
    }
  },
  created() {
    this.updateCaptchaCode()
  }
}
</script>

<style scoped>
  .pointer {
    cursor: pointer;
  }
</style>
