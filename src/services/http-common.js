import axios from 'axios'
const config = require('../../config/reports')
const API_TOKEN = config.API_TOKEN
export const BASE_URL = config.BASE_URL

export const HTTP = axios.create({
  baseURL: BASE_URL,
  headers: {
    Authorization: API_TOKEN
  }
})
