class Store {
    #listeners = []
    #state

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

    /**
     * @param {Object} data
     */
    update(data) {
        let newData = JSON.parse(JSON.stringify(data))
        let oldData = Object.fromEntries(
            new Map(
                Object.keys(data).map(prop => {
                    return [prop, this.#state[prop]]
                })
            )
        )
        Object.entries(newData).forEach(([prop, value]) => {
            this.#state[prop] = value
        })

        this.#notify(Object.keys(newData), oldData, newData)
    }

    /**
     * @param {String} prop
     * @param {Function} callback
     */
    set(prop, callback) {
        const oldState = {
            [[prop]]: this.#state,
        }
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
        const callback = (p, newObject, oldObject) => {
            const requiredParts = Array.isArray(prop) ? prop : prop.split('.')

            let ctx = {
                [[p]]: newObject,
            }
            let oldCx = {
                [[p]]: oldObject,
            }
            while (requiredParts.length > 0) {
                const part = requiredParts.shift()
                if (typeof ctx[part] === 'undefined') {
                    return false
                }
                ctx = ctx[part]

                if (typeof oldCx !== 'undefined') {
                    if (typeof oldCx[part] === 'undefined') {
                        oldCx = undefined
                    } else {
                        oldCx = oldCx[part]
                    }
                }
            }

            // listener(ctx, prop, p, newObject)
            if (ctx !== oldCx) {
                listener(ctx, prop, p, newObject)
            }
        }

        this.#listeners.push(callback)

        return this
    }

    #notify(changedProps, oldData, newData) {
        Promise.resolve().then(() => {
            this.#listeners.forEach(listener => {
                changedProps.forEach(prop => {
                    listener(prop, newData[prop], oldData[prop])
                })
            })
        })
    }
}

/**
 * @param {Object} data
 */
export default function createStore(data = {}) {
    return new Store(data)
}
