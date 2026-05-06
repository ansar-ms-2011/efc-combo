import { reactive } from "vue"

const state = reactive({
    dialogs: []
})

function open(component, props = {}) {
    return new Promise((resolve, reject) => {

        const dialog = {
            component,
            props,
            resolve,
            reject
        }

        state.dialogs.push(dialog)
    })
}

function close(dialog, result = null) {
    const index = state.dialogs.indexOf(dialog)

    if (index > -1) {
        state.dialogs.splice(index, 1)
        dialog.resolve(result)
    }
}

export default {
    state,
    open,
    close
}
