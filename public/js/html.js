/* global $ */

export default {
    e(string) {
        return $('<div>').text(string).html()
    },

    data(data) {
        return Object.entries(data)
            .map(([key, value]) => {
                return 'data-' + this.attr(key, value)
            })
            .join(' ')
    },

    attr(name, value) {
        if (!/[-_a-z0-9]/gi.test(name)) {
            throw new Error('Invalid attribute name: ' + name)
        }

        if (!/[-_a-z0-9]/gi.test(value)) {
            throw new Error('Invalid attribute value: ' + value)
        }

        return `${name}="${value}"`
    },

    attrs(data) {
        const parts = Object.entries(data).map(([key, value]) => {
            return this.attr(key, value)
        })

        return parts.join(' ')
    },
    map(list, callback) {
        return list.map(callback).join('\n')
    },
}
