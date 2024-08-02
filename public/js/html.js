/* global $ */

class Renderer {
    counter = 0
    storage = {}

    nextId = () => {
        ++this.counter

        return this.counter
    }

    render(templateFnc) {
        const renderer = this

        const renderedTemplate = templateFnc({
            assign: (key, data) => {
                const id = renderer.nextId()
                this.storage[id] = data
                return `data-element-id="${id}" data-element-key=${key}`
            },
        })

        const $el = $('<div></div>').append($(renderedTemplate))

        return $el.html()
    }

    assign($el) {
        $el.find('[data-element-id]').each((index, el) => {
            const id = $(el).data('elementId')
            const key = $(el).data('elementKey')

            $(el).removeAttr('data-element-id').removeAttr('data-element-key')

            $(el).data({
                [key]: this.storage[id],
            })
        })
    }
}

let renderer = null

export default {
    e(string) {
        return $('<div>').text(string).html()
    },

    rendererSingleton() {
        if (!renderer) {
            renderer = new Renderer()
        }

        return renderer
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
