/* global $ */
class TreeComponent {
    static EVENT_ADD_NODE = 'add-node'
    static EVENT_DELETE_NODE = 'delete-node'

    $container;
    tree;
    $treeContainer;
    constructor($container) {
        this.tree = null
        this.$container = $container;
    }

    init() {
        this.$treeContainer = $('<div class="tree-container"></div>')
            .appendTo(this.$container)

        const $bus = $(this);

        this.$container.on('click', '.item-add', (e) => {
            const $el = $(e.target)
            const id = $el.closest('.item').data('id')

            $bus.trigger(TreeComponent.EVENT_ADD_NODE, [{id}])
        })

        this.$container.on('click', '.item-delete', (e) => {
            const $el = $(e.target)
            const id = $el.closest('.item').data('id')

            $bus.trigger(TreeComponent.EVENT_DELETE_NODE, [{id}])
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
            const $controls = $(
                '<span class="controls">' +
                '   <button type="button" class="btn btn-light item-add">+</button>' +
                '   <button type="button" class="btn btn-light item-delete">-</button>' +
                '</span>'
            )

            const $item = $(`
                    <div class="item">
                        <p class="text"></p>
                        <div class="children"></div>
                    </div>
                `)
            $item
                .find('.text')
                .text(child.name)
                .append($controls)

            $item.data({
                id: child.id,
            })

            if (child.children.length > 0) {
                const $childrenContainer = $item.find('.children');
                this.#createNodes(child.children, $childrenContainer)
            }

            $container.append($item)
        })
    }
}

export default TreeComponent
