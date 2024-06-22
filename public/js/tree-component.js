/* global $ */

import {makeObservable, makeObserver} from "./observable.js";

class TreeComponent {
    static EVENT_ADD_NODE = 'add-node'
    static EVENT_DELETE_NODE = 'delete-node'
    static EVENT_UPDATE_NODE = 'update-node'

    $container
    $treeContainer
    /** @type {Store} */
    store

    constructor($container) {
        this.store = makeObservable({
            tree: {},
            nodes: {},
            editableNodeId: 0,
        })
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
            .on('click', '.item-open', this.onNodeOpen.bind(this))
            .on('click', '.item-close', this.onNodeClose.bind(this))
            .on('click', '.item-cancel', this.onNodeEditCancel.bind(this))

        makeObserver(this.#render.bind(this))
    }

    onNodeOpen(e) {
        const $el = $(e.target)
        const id = $el.closest('.item').data('nodeId')
        this.store.nodes[id].isOpen = true
    }

    onNodeClose(e) {
        const $el = $(e.target)
        const id = $el.closest('.item').data('nodeId')
        this.store.nodes[id].isOpen = false
    }

    onNodeSave(e) {
        const $el = $(e.target)
        const item = $el.closest('.item')
        const id = $el.closest('.item').data('nodeId')
        const newName = item.find('.main:first [name="node_name"]').val()

        this.store.editableNodeId = 0

        this.trigger(TreeComponent.EVENT_UPDATE_NODE, [{ id, name: newName }])
    }

    onNodeEdit(e) {
        const $el = $(e.target)
        const id = $el.closest('.item').data('nodeId')

        this.store.editableNodeId = id
    }

    onNodeEditCancel() {
        this.store.editableNodeId = 0
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
    /*listen(prop, listener) {
        this.store.listen(prop, listener)
    }*/

    #render() {
        if (!this.store.tree.root) {
            return
        }

        const $treeContainer = $('<div class="tree-container"></div>')
        this.#createNodes([this.store.tree.root], $treeContainer)
        this.$treeContainer.replaceWith($treeContainer)
        this.$treeContainer = $treeContainer
    }

    setState(tree) {
        const nodeDefaults = {
            isOpen: true,
        }

        const nodes = treeToArrayOfNodes(tree.root)
            .map(node => {
                return {
                    ...node,
                    ...overwrite(nodeDefaults, this.store.nodes[node.id])
                }
            })
        const nodesState = indexBy(nodes, 'id')

        this.store.tree = tree
        this.store.nodes = nodesState
    }

    #createNodes(nodesStateList, $container) {
        nodesStateList.forEach(({ id }) => {
            const node = this.store.nodes[id]
            const $item = $(`
                <div class="item">
                    <div class="main">
                        <div>
                            <span class="name"></span>
                            <span class="input-name"><input name="node_name" type="text"></span>
                            <span class="controls">
                               <button type="button" class="btn btn-light item-add">+</button>
                               <button type="button" class="btn btn-light item-delete">-</button>
                               <button type="button" class="btn btn-light item-edit">edit</button>
                               <button type="button" class="btn btn-light item-cancel">cancel</button>
                               <button type="button" class="btn btn-light item-save">save</button>
                            </span>
                        </div>
                        ${
                            node.children.length > 0
                                ? `<div class="children-controls">
                                        <span>
                                            <button type="button" class="btn btn-light item-open"></button>
                                            <button type="button" class="btn btn-light item-close">close</button>
                                        </span>
                                    </div>`
                                : ''
                        }
                        
                    </div>
                    <div class="children"></div>
                </div>
            `)
            const $main = $item.find('.main')
            const store = this.store

            $item.attr('data-node-id', node.id)
            $main.find('.item-open').text(`open (${node.children.length})`)

            makeObserver(() => {
                $main.find('.name').text(node.name)
                $main.find('[name="node_name"]').val(node.name)

                const update = {
                    name() {
                        $main.find('.name').text(node.name)
                        $main.find('[name="node_name"]').val(node.name)

                        return this
                    },
                    isOpen() {
                        const $container = $item.find('.children:first')
                        const $openBtn = $main.find('.item-open')
                        const $closeBtn = $main.find('.item-close')

                        if (node.isOpen) {
                            $container.show()
                            $closeBtn.show()
                            $openBtn.hide()
                        } else {
                            $container.hide()
                            $closeBtn.hide()
                            $openBtn.show()
                        }

                        return this
                    },
                    editableNodeId() {
                        const $editGroup = $main.find(
                            '.input-name, .item-cancel, .item-save'
                        )
                        const $defaultGroup = $main.find('.name, .item-edit')

                        if (store.editableNodeId === node.id) {
                            $main
                                .find('[name="node_name"]')
                                .val(node.name)
                            $editGroup.show()
                            $defaultGroup.hide()
                        } else {
                            $editGroup.hide()
                            $defaultGroup.show()
                        }

                        return this
                    },
                }

                update
                    .name()
                    .isOpen()
                    .editableNodeId()
            })

            if (node.children.length > 0) {
                const $childrenContainer = $item.find('.children')
                this.#createNodes(node.children, $childrenContainer)
            }

            $container.append($item)
        })
    }
}

function treeToArrayOfNodes(root) {
    const queue = [root]
    const result = []
    while (queue.length > 0) {
        const node = queue.shift()
        result.push(node)

        node.children.forEach(child => {
            queue.push(child)
        })
    }

    return result
}

/**
 * @param {Object} obj
 * @param {Object|undefined} other
 * @returns {Object}
 */
function overwrite(obj, other) {
    if (typeof other === 'undefined') {
        return obj
    }

    const result = {...obj}
    Object.keys(result).forEach(key => {
        if (Object.prototype.hasOwnProperty.call(other, key)) {
            result[key] = other[key]
        }
    })

    return result
}

/**
 * @param {Array} array
 * @param {Function|string} indexer
 */
function indexBy(array, indexer) {
    const indexFnc = typeof indexer === 'string'
        ? (item) => item[indexer]
        : indexer

    const entries = array.map(item => {
        return [
            indexFnc(item),
            item,
        ]
    })

    return Object.fromEntries(new Map(entries))
}

export default TreeComponent
