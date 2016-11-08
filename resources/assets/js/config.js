// TODO add MODE_ENV
export const API_ROOT = (process.env.NODE_ENV === 'production')
      ? 'https://vkextractor.ch'
      : 'http://localhost:8000/api'
