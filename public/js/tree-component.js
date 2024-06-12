/* global $ */
import html from './html.js'

class TreeComponent {
    static EVENT_ADD_NODE = 'add-node'
    static EVENT_DELETE_NODE = 'delete-node'
    static EVENT_UPDATE_NODE = 'update-node'

    $container
    tree
    $treeContainer

    constructor($container) {
        this.tree = null
        this.$container = $container
    }

    init() {
        this.$treeContainer = $('<div class="tree-container"></div>').appendTo(
            this.$container
        )

        const $bus = $(this)

        this.$container.on('click', '.item-add', e => {
            const $el = $(e.target)
            const id = $el.closest('.item').data('id')

            $bus.trigger(TreeComponent.EVENT_ADD_NODE, [{ id }])
        })

        this.$container.on('click', '.item-delete', e => {
            const $el = $(e.target)
            const id = $el.closest('.item').data('id')

            $bus.trigger(TreeComponent.EVENT_DELETE_NODE, [{ id }])
        })

        this.$container.on('click', '.item-edit', e => {
            const $el = $(e.target)
            $el.closest('.item')
                .find('p.text .name')
                .prop('contentEditable', 'plaintext-only')
        })

        this.$container.on('click', '.item-save', e => {
            const $el = $(e.target)
            const item = $el.closest('.item')
            const id = $el.closest('.item').data('id')
            const newName = item.find('p.text .name').text()

            $el.closest('.item')
                .find('p.text .name')
                .prop('contentEditable', 'false')

            $bus.trigger(TreeComponent.EVENT_UPDATE_NODE, [
                { id, name: newName },
            ])
        })
    }

    render() {
        const $treeContainer = $('<div class="tree-container"></div>')
        this.#createNodes([this.tree.root], $treeContainer)
        this.$treeContainer.replaceWith($treeContainer)
        this.$treeContainer = $treeContainer
    }

    setState(tree) {
        this.tree = tree
    }

    #renderNode(child) {
        return `
            <div class="item" ${html.data({ id: child.id, child })}>
                <p class="text">
                    <span class="name">${html.e(child.name)}</span>
                    <span ${html.attrs({ class: 'controls' })}>
                       <button type="button" class="btn btn-light item-add">+</button>
                       <button type="button" class="btn btn-light item-delete">-</button>
                       <button type="button" class="btn btn-light item-edit">edit</button>
                       <button type="button" class="btn btn-light item-save">save</button>
                    </span>
                </p>
                <div class="children">
                    ${html.map(child.children, child => {
                        return this.#renderNode(child)
                    })}
                </div>
            </div>
        `
    }

    #createNodes(nodesStateList, $treeContainer) {
        const parts = nodesStateList.map(child => {
            return this.#renderNode(child)
        })
        const $el = $(parts.join('\n'))
        $treeContainer.append($el)
    }
}

export default TreeComponent
