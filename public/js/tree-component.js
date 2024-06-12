/* global $ */
import html from './html.js'

class TreeComponent {
    static EVENT_ADD_NODE = 'add-node'
    static EVENT_DELETE_NODE = 'delete-node'
    static EVENT_UPDATE_NODE = 'update-node'

    $container
    tree
    $treeContainer
    r
    renderer

    constructor($container) {
        this.tree = null
        this.$container = $container
        this.renderer = html.rendererSingleton()
        this.r = this.renderer.render.bind(this.renderer)
    }

    init() {
        this.$treeContainer = $('<div class="tree-container"></div>').appendTo(
            this.$container
        )

        const $bus = $(this)

        this.$container.on('click', '.item-add', e => {
            const id = $(e.target).data('node_id')

            $bus.trigger(TreeComponent.EVENT_ADD_NODE, [{ id }])
        })

        this.$container.on('click', '.item-delete', e => {
            const id = $(e.target).data('node_id')

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
            const $item = $el.closest('.item')
            const id = $el.data('node_id')
            const newName = $item.find('p.text .name').text()

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
        const $el = this.#createNodes([this.tree.root])
        $treeContainer.append($el)
        this.$treeContainer.replaceWith($treeContainer)
        this.$treeContainer = $treeContainer
        this.renderer.assign($treeContainer)
    }

    setState(tree) {
        this.tree = tree
    }

    #renderNode(child) {
        return this.r(({ assign }) => {
            return `
                <div 
                    class="item"
                    ${assign('node', child)}
                >
                    <p class="text">
                        <span class="name">${html.e(child.name)}</span>
                        <span ${html.attrs({ class: 'controls' })}>
                           <button type="button" class="btn btn-light item-add" ${assign('node_id', child.id)}>+</button>
                           <button type="button" class="btn btn-light item-delete" ${assign('node_id', child.id)}>-</button>
                           <button type="button" class="btn btn-light item-edit" ${assign('node_id', child.id)}>edit</button>
                           <button type="button" class="btn btn-light item-save" ${assign('node_id', child.id)}>save</button>
                        </span>
                    </p>
                    <div class="children">
                        ${html.map(child.children, child => {
                            return this.#renderNode(child)
                        })}
                    </div>
                </div>
            `
        })
    }

    #createNodes(nodesStateList) {
        const parts = nodesStateList.map(child => {
            return this.#renderNode(child)
        })

        return $(parts.join('\n'))
    }
}

export default TreeComponent
