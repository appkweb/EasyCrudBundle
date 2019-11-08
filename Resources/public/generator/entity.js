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
    document.getElementById('loader-bar').style.display = "block";
    var xhttp;
    var params = getEntityData();
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('loader-bar').style.display = "none";
            var data = JSON.parse(xhttp.response);
            location.href = data.location;
        }
    };
    xhttp.open("POST", path, true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send(params);
}

/**
 * Get Str params of entity
 * @returns {string}
 */
function getEntityData() {
    var className = document.getElementById("entity_def_className").value;
    var label = document.getElementById("entity_def_label").value;
    var singular_label = document.getElementById("entity_def_singularLabel").value;
    var plurial_label = document.getElementById("entity_def_plurialLabel").value;
    var order = document.getElementById("entity_def_order").value;
    var referrer = document.getElementById("entity_def_referrer").value;
    var visible = document.getElementById("entity_def_visible").checked;
    var remove = document.getElementById("entity_def_remove").checked;
    var list = document.getElementById("entity_def_list").checked;
    var edit = document.getElementById("entity_def_edit").checked;
    var add = document.getElementById("entity_def_add").checked;
    var show = document.getElementById("entity_def_show").checked;
    var attributes = [];
    var myTab = document.getElementById('datatable');
    var tab = $('#dataTable').DataTable();
    var rows = tab.$('tr');

    Object.size = function (obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size - 1;
    };
    var size = Object.size(rows);

    var oldClassname = document.getElementById('old-classname');
    if (oldClassname) {
        oldClassname = oldClassname.textContent
    } else {
        oldClassname = false;
    }

    for (i = 1; i < size; i++) {
        var objCells = rows[i].$('td');
        var entity_relation = objCells[3].textContent.split('Entity linked : ')
        attr_type = objCells[3].textContent;
        if (entity_relation.length > 1) {
            attr_type = entity_relation[0]
            entity_relation = entity_relation[1]
        } else {
            entity_relation = false;
        }
        cell = {
            attr_name: objCells[1].textContent,
            attr_label: objCells[2].textContent,
            attr_type: attr_type,
            attr_list: objCells[4].textContent,
            attr_edit: objCells[5].textContent,
            attr_show: objCells[6].textContent,
            attr_nullable: objCells[7].textContent,
            attr_unique: objCells[8].textContent,
            attr_order: objCells[9].textContent,
            attr_entity_relation: entity_relation,
        }
        attributes.push(cell);
    }

    var strData = '&className=' + className
        + '&oldClassName=' + oldClassname
        + '&label=' + label
        + '&singular_label=' + singular_label
        + '&plurial_label=' + plurial_label
        + '&order=' + order
        + '&visible=' + visible
        + '&remove=' + remove
        + '&referrer=' + referrer
        + '&list=' + list
        + '&edit=' + edit
        + '&add=' + add
        + '&show=' + show
        + '&attributes=' + JSON.stringify(attributes)

    return strData;
}

