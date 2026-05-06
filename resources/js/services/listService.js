import axios from 'axios'

export const fetchList = async (endPoint, page = 1, type = null,) => {
  const params = { page }

  if (type !== null && type !== undefined && type !== '') {
    params.type = type
  }

  const response = await axios.get(`/api/${endPoint}`, {
    params,
    headers: {
      Authorization: `Bearer ${sessionStorage.getItem('accessToken')}`
    }
  })

  const data = response.data.data

  return {
    items: data.data,
    currentPage: data.current_page,
    lastPage: data.last_page,
    perPage: data.per_page
  }
}
