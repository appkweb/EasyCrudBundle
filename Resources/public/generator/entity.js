/*
 * This file is part of the Appkweb package.
 *
 * (c) Valentin REGNIER <vregnier@appkweb.com>
 *
 * Contributors :
 * - REGNIER Valentin
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *
 * This JS file contain all necessary function used by template of entity generator
 */

/**
 * Save entity data in Yaml files
 * @param e
 * @param path
 */
function save(e, path) {
    e.preventDefault();
    var xhttp;
    var params = getEntityData();
    xhttp = new XMLHttpRequest();
    xhttp.open("POST", path, true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send(params);
}

/**
 * Get Str params of entity
 * @returns {string}
 */
function getEntityData() {
    var entityName = document.getElementById("entity_def_entityName").value;
    var className = document.getElementById("entity_def_className").value;
    var prefix = document.getElementById("entity_def_prefix").value;
    var label = document.getElementById("entity_def_label").value;
    var order = document.getElementById("entity_def_order").value;
    var visible = document.getElementById("entity_def_visible").checked;
    var remove = document.getElementById("entity_def_remove").checked;
    var list = document.getElementById("entity_def_list").checked;
    var edit = document.getElementById("entity_def_edit").checked;
    var add = document.getElementById("entity_def_add").checked;
    var attributes = [];
    var myTab = document.getElementById('datatable');

    for (i = 1; i < myTab.rows.length; i++) {
        var objCells = myTab.rows.item(i).cells;
        cell = {
            attr_name: objCells.item(1).textContent,
            attr_label: objCells.item(2).textContent,
            attr_type: objCells.item(3).textContent,
            attr_entity_relation: objCells.item(4).textContent,
            attr_size: objCells.item(5).textContent,
            attr_nullable: objCells.item(6).textContent,
            attr_visible: objCells.item(7).textContent,
            attr_order: objCells.item(8).textContent,
        }
        attributes.push(cell);
    }

    var strData = 'entityName=' + entityName
        + '&className=' + className
        + '&prefix=' + prefix
        + '&label=' + label
        + '&order=' + order
        + '&visible=' + visible
        + '&remove=' + remove
        + '&list=' + list
        + '&edit=' + edit
        + '&add=' + add
        + '&attributes=' + JSON.stringify(attributes)

    return strData;
}

