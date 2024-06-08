let autoIncrement = 5;
let tree = {
    root: {
        id: 1,
        name: "Content Root method=GET",
        children: [
            {
                id: 2,
                name: "Item 1-1",
                children: [
                    {
                        id: 4,
                        name: "Item 1-1-1",
                        children: []
                    },
                    {
                        id: 5,
                        name: "Item 1-1-2",
                        children: []
                    },
                ]
            },
            {
                id: 3,
                name: "Item 1-2",
                children: []
            },
        ],
    }
};

function mapTree(node, callback) {
    return {
        ...callback(node),
        children: node.children.map(child => mapTree(child, callback))
    }
}

export const getAllNodes = () => {
    return Promise.resolve({
        success: true,
        data: tree
    })

    /*return fetch("/get-all")
        .then(resp => {
            return resp.json()
        })
        .catch(e => {
            return new Error(`Can not get nodes: ${e.message}`)
        })*/
}

export const createNode = (parentId) => {
    autoIncrement++
    const newId = autoIncrement;

    tree = {
        ...tree,
        root: mapTree(tree.root, node => {
            if (node.id === parentId) {
                node.children.push({
                    id: newId,
                    name: `Item ${newId}`,
                    children: []
                })
            }

            return node
        })
    }

    return Promise.resolve()
    /*fetch(`/create?parent_id=${parentId}`, {
        method: 'POST'
    })*/
}

/**
 * @param {Number} id
 * @return Promise
 */
export const deleteNode = (id) => {
    fetch(`/delete?id=${id}`, {
        method: 'POST',
    })
}

export default {
    getAllNodes,
    createNode,
    deleteNode,
};
