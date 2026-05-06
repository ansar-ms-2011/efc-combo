import localforage from 'localforage'

export const cache = localforage.createInstance({
    name: 'domicile-admin'
})
