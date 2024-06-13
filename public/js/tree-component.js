/* global $ */

import createStore from './store.js'

class TreeComponent {
    static EVENT_ADD_NODE = 'add-node'
    static EVENT_DELETE_NODE = 'delete-node'
    static EVENT_UPDATE_NODE = 'update-node'

    $container
    $treeContainer
    /** @type {Store} */
    store
    state

    constructor($container) {
        this.store = createStore({
            tree: {},
            nodes: {},
        })
        // this.state = this.store.state
        this.$container = $container
    }

    init() {
        this.$treeContainer = $('<div class="tree-container"></div>').appendTo(
            this.$container
        )

        this.$container
            .on('click', '.item-add', this.onNodeAdd.bind(this))
            .on('click', '.item-delete', this.onNodeDelete.bind(this))
            .on('click', '.item-edit', this.onNodeEdit.bind(this))
            .on('click', '.item-save', this.onNodeSave.bind(this))

        this.listen('tree', tree => {
            this.#render()
        })
    }

    onNodeSave(e) {
        const $el = $(e.target)
        const item = $el.closest('.item')
        const id = $el.closest('.item').data('nodeId')
        const newName = item.find('.main:first [name="node_name"]').val()

        this.store.set('nodes', nodes => {
            nodes[id].isEditable = false
        })

        this.trigger(TreeComponent.EVENT_UPDATE_NODE, [{ id, name: newName }])
    }

    onNodeEdit(e) {
        const $el = $(e.target)
        const id = $el.closest('.item').data('nodeId')

        this.store.set('nodes', nodes => {
            nodes[id].isEditable = true
        })
    }

    onNodeDelete(e) {
        const $el = $(e.target)
        const id = $el.closest('.item').data('nodeId')

        this.trigger(TreeComponent.EVENT_DELETE_NODE, [{ id }])
    }

    onNodeAdd(e) {
        const $el = $(e.target)
        const id = $el.closest('.item').data('nodeId')

        this.trigger(TreeComponent.EVENT_ADD_NODE, [{ id }])
    }

    trigger(event, data) {
        $(this).trigger(event, data)
    }

    /**
     * @param {Array|String} prop
     * @param {Function} listener
     */
    listen(prop, listener) {
        this.store.listen(prop, listener)
    }

    #render() {
        const $treeContainer = $('<div class="tree-container"></div>')
        this.#createNodes([this.store.tree.root], $treeContainer)
        this.$treeContainer.replaceWith($treeContainer)
        this.$treeContainer = $treeContainer
    }

    setState(tree) {
        const nodeDefaults = {
            isEditable: false,
            isOpen: false,
        }

        const nodes = mapNodesToObject(tree.root, node => {
            const nodeState =
                typeof this.store.nodes[node.id] === 'undefined'
                    ? nodeDefaults
                    : {
                          isEditable: this.store.nodes[node.id].isEditable,
                          isOpen: this.store.nodes[node.id].isOpen,
                      }

            return [
                node.id,
                {
                    ...node,
                    ...nodeState,
                },
            ]
        })

        this.store.update({
            tree: {},
            nodes: {},
        })
        this.store.update({
            tree: tree,
        })
        this.store.update({
            nodes: nodes,
        })
    }

    #createNodes(nodesStateList, $container) {
        nodesStateList.forEach(node => {
            const $item = $(`
                    <div class="item">
                        <p class="main">
                            <span class="name"></span>
                            <span class="input-name"><input name="node_name" type="text"></span>
                            <span class="controls">
                               <button type="button" class="btn btn-light item-add">+</button>
                               <button type="button" class="btn btn-light item-delete">-</button>
                               <button type="button" class="btn btn-light item-edit">edit</button>
                               <button type="button" class="btn btn-light item-cancel">cancel</button>
                               <button type="button" class="btn btn-light item-save">save</button>
                            </span>
                        </p>
                        <span>
                            <button type="button" class="btn btn-light item-open">open</button>
                            <button type="button" class="btn btn-light item-close">close</button>
                        </span>
                        <div class="children"></div>
                    </div>
                `)
            const $main = $item.find('.main')
            // $item.find('.main .name').text(node.name)

            $item.attr('data-node-id', node.id)

            const onChange = {
                name: name => {
                    $main.find('.name').text(name)
                    $main.find('[name="node_name"]').val(name)
                },
                isOpen: isOpen => {
                    const $container = $main.find(
                        `[data-node-id="${node.id}"] .children:first`
                    )

                    isOpen ? $container.show() : $container.hide()
                },
                isEditable: isEditable => {
                    const $editGroup = $main.find(
                        '.input-name, .item-cancel, .item-save'
                    )
                    const $defaultGroup = $main.find('.name, .item-edit')
                    if (isEditable) {
                        $main
                            .find('[name="node_name"]')
                            .val(this.store.nodes[node.id].name)
                        $editGroup.show()
                        $defaultGroup.hide()
                    } else {
                        $editGroup.hide()
                        $defaultGroup.show()
                    }
                },
            }

            this.store
                .listen(`nodes.${node.id}.name`, onChange.name.bind(this))
                .listen(`nodes.${node.id}.isOpen`, onChange.isOpen.bind(this))
                .listen(
                    `nodes.${node.id}.isEditable`,
                    onChange.isEditable.bind(this)
                )

            if (node.children.length > 0) {
                const $childrenContainer = $item.find('.children')
                this.#createNodes(node.children, $childrenContainer)
            }

            $container.append($item)
        })
    }
}

function mapNodesToObject(root, callback) {
    const queue = [root]
    const entries = []
    while (queue.length > 0) {
        const item = queue.shift()
        entries.push(callback(item))
        item.children.forEach(c => queue.push(c))
    }

    return Object.fromEntries(new Map(entries))
}

export default TreeComponent
