class Store {
    #listeners = []
    #state
    /** @type {Array|null} */
    #group = null

    /**
     * @param {Object} data
     */
    constructor(data) {
        this.#state = data

        Object.keys(data).forEach(key => {
            Object.defineProperty(this, key, {
                get() {
                    return this.#state[key]
                },
                set(newValue) {
                    this.#state[key] = newValue
                },
                enumerable: true,
                configurable: true,
            })
        })
    }

    startGroup() {
        this.#group = []

        return this
    }

    popGroup() {
        const group = this.#group
        this.#group = null

        return {
            notify: () => {
                group.forEach(({ prop, listener }) => {
                    const value = getProp(prop, this.#state)
                    listener(value, undefined, prop)
                })
            },
        }
    }

    /**
     * @param {Object} data
     */
    update(data) {
        let oldData = JSON.parse(JSON.stringify(this.#state))
        Object.entries(data).forEach(([prop, value]) => {
            this.#state[prop] = value
        })

        this.#notify(Object.keys(data), oldData, data)
    }

    /**
     * @param {String} prop
     * @param {Function} callback
     */
    set(prop, callback) {
        const oldState = JSON.parse(JSON.stringify(this.#state))
        const newData = JSON.parse(JSON.stringify(this.#state[prop]))
        callback(newData)
        this.#state[prop] = { ...newData }

        this.#notify([prop], oldState, this.#state)
    }

    /**
     * @param {Array|String} prop
     * @param {Function} listener
     * @return {Store}
     */
    listen(prop, listener) {
        const callback = (p, newValue, oldValue) => {
            if (newValue !== oldValue) {
                listener(newValue, oldValue, prop)
            }
        }

        this.#listeners.push({
            prop,
            listener: callback,
        })

        if (this.#group !== null) {
            this.#group.push({
                prop,
                listener,
            })
        }

        return this
    }

    #notify(changedProps, oldData, newData) {
        const onfulfilled = () => {
            this.#listeners.forEach(({ prop, listener }) => {
                changedProps.forEach(changedProp => {
                    if (!prop.startsWith(changedProp)) {
                        return
                    }

                    listener(
                        prop,
                        getProp(prop, newData),
                        getProp(prop, oldData)
                    )
                })
            })
        }
        Promise.resolve().then(onfulfilled)
    }
}

function getProp(prop, object, defaultValue = undefined) {
    const parts = Array.isArray(prop) ? prop : prop.split('.')

    let value = object
    while (parts.length > 0) {
        const part = parts.shift()
        if (typeof value[part] === 'undefined') {
            return defaultValue
        }
        value = value[part]
    }

    return value
}

/**
 * @param {Object} data
 */
export default function createStore(data = {}) {
    return new Store(data)
}
