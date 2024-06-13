class Store {
    listeners = []
    state

    /**
     * @param {Object} data
     */
    constructor(data) {
        const listeners = this.listeners
        this.state = new Proxy(data, {
            set(target, p, newValue) {
                const oldValue = target[p]
                target[p] = newValue

                if (oldValue !== newValue) {
                    Promise.resolve().then(() => {
                        listeners.forEach(listener => {
                            listener(p, newValue, oldValue)
                        })
                    })
                }

                return true
            },
        })
    }

    /**
     * @param {Object} data
     */
    set(data) {
        let newData = JSON.parse(JSON.stringify(data))

        Object.entries(newData).forEach(([prop, value]) => {
            this.state[prop] = value
        })
    }

    /**
     * @param {String} prop
     * @param {Function} callback
     */
    update(prop, callback) {
        let item = this.state[prop]
        item = JSON.parse(JSON.stringify(item))
        callback(item)
        // let newItem = callback(item)
        this.state[prop] = { ...item }
    }

    /**
     * @param {Array|String} prop
     * @param {Function} listener
     */
    listen(prop, listener) {
        const callback = (p, newValue, oldValue) => {
            const requiredParts = Array.isArray(prop) ? prop : prop.split('.')

            let ctx = {
                [[p]]: newValue,
            }
            while (requiredParts.length > 0) {
                const part = requiredParts.shift()
                if (typeof ctx[part] === 'undefined') {
                    return false
                }
                ctx = ctx[part]
            }

            listener(ctx, prop, p, newValue)
        }

        this.listeners.push(callback)
    }
}

/**
 * @param {Object} data
 */
export default function createStore(data = {}) {
    return new Store(data)
}
