/**
 * Flexi Helper Functions
 */

/**
 * Group terms by parent
 *
 * @since 1.0.0
 */
export function GroupByParent(terms) {
	var map = {},
		node,
		roots = [],
		i;

	for (i = 0; i < terms.length; i += 1) {
		map[terms[i].id] = i; // initialize the map
		terms[i].children = []; // initialize the children
	}

	for (i = 0; i < terms.length; i += 1) {
		node = terms[i];
		if (node.parent > 0) {
			terms[map[node.parent]].children.push(node);
		} else {
			roots.push(node);
		}
	}

	return roots;
}

/**
 * Build tree array from flat array
 *
 * @since 1.0.0
 */
export function BuildTree(terms, tree = [], prefix = "") {
	var i;

	for (i = 0; i < terms.length; i += 1) {
		tree.push({
			label: prefix + terms[i].name,
			value: terms[i].id,
		});

		if (terms[i].children.length > 0) {
			BuildTree(terms[i].children, tree, prefix.trim() + "--- ");
		}
	}

	return tree;
}
