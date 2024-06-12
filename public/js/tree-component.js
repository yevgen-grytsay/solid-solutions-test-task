/* global $ */
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

    #createNodes(nodesStateList, $container) {
        nodesStateList.forEach(child => {
            const $controls = $(`
                <span class="controls">
                   <button type="button" class="btn btn-light item-add">+</button>
                   <button type="button" class="btn btn-light item-delete">-</button>
                   <button type="button" class="btn btn-light item-edit">edit</button>
                   <button type="button" class="btn btn-light item-save">save</button>
                </span>
            `)

            const $item = $(`
                    <div class="item">
                        <p class="text"><span class="name"></span></p>
<!--                        <div class="name-input"><input type="text"></div>-->
                        <div class="children"></div>
                    </div>
                `)
            $item.find('.text').append($controls)
            $item.find('.text .name').text(child.name)

            $item.data({
                id: child.id,
            })

            if (child.children.length > 0) {
                const $childrenContainer = $item.find('.children')
                this.#createNodes(child.children, $childrenContainer)
            }

            $container.append($item)
        })
    }
}

export default TreeComponent
