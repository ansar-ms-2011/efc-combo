import Swal from 'sweetalert2'
import axios from 'axios'

export const confirmAndDelete = async (endPoint, id) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: 'Delete this record?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  })

  if (!result.isConfirmed) return false

  try {
    await axios.delete(`/api/${endPoint}/${id}`, {
      headers: {
        Authorization: `Bearer ${sessionStorage.getItem('accessToken')}`
      }
    })

    await Swal.fire('Deleted!', 'Record has been deleted.', 'success')
    return true
  } catch (error) {
    await Swal.fire('Error!', 'Failed to delete record.', 'error')
    throw error
  }
}
