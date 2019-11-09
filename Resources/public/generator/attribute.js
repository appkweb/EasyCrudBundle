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


// VARIABLES
var attr_name;
var attr_label;
var attr_type;
var relation_entity;
var order;
var can_be_null;
var can_be_listed;
var can_be_showed;
var can_be_edited;
var unique;
var current_edit = false;

// EXECUTED ON PAGE LOAD
(function () {


})();

/**
 *
 */
function checkType() {
    size = true;
    attr_type = document.getElementById('attribute_def_type').value;
    switch (true) {
        case attr_type == "Simple select" || attr_type == "Add list" || attr_type == "Multiple select":
            document.getElementById('entity-container').style.display = "block";
            break;
        default:
            document.getElementById('entity-container').style.display = "none";
            relation_entity = false;
    }
}

//FUNCTIONS

/**
 * This function load current datas
 */
function loadData() {
    attr_name = document.getElementById('attribute_def_name').value;
    attr_label = document.getElementById('attribute_def_label').value;
    attr_type = document.getElementById('attribute_def_type').value;
    order = document.getElementById('attribute_def_order').value;
    relation_entity = document.getElementById('attribute_def_entity').value;
    can_be_null = document.getElementById('attribute_def_nullable').checked;
    can_be_edited = document.getElementById('attribute_def_edit').checked;
    can_be_showed = document.getElementById('attribute_def_show').checked;
    can_be_listed = document.getElementById('attribute_def_list').checked;
    unique = document.getElementById('attribute_def_unique').checked;

}

/**
 * This function was used on click event of "Add attribute" button to know if it's an edit or new attr
 */
function newAttr() {
    var selectReferrer = document.getElementById('attribute_def_entity');
    for (var i, j = 0; i = selectReferrer.options[j]; j++) {
        mySelect.selectedIndex = j;
        break;
    }
    clear();
    current_edit = false;
}

/**
 * This function reset value of attribute's form
 */
function clear() {
    document.getElementById('attribute_def_name').value = '';
    document.getElementById('attribute_def_label').value = '';
    document.getElementById('attribute_def_type').value = 'Number';
    document.getElementById('attribute_def_order').value = '';
    document.getElementById('attribute_def_entity').value = '';
    document.getElementById('attribute_def_nullable').checked = false;
    document.getElementById('attribute_def_list').checked = false;
    document.getElementById('attribute_def_show').checked = false;
    document.getElementById('attribute_def_edit').checked = false;
    document.getElementById('attribute_def_unique').checked = false;
    checkType();
}

/**
 * This function add attr to the table and stop propagation of submit form
 * @param e -> Event
 */
function add(e) {
    e.preventDefault();
    loadData();
    checkType()
    updateTable();
    addReferrer(attr_name);
    $('#myModal').modal('hide');
}

/**
 * This function add new data in table
 */
function updateTable() {
    var t = $('#datatable').DataTable({
        "destroy": true,
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "pageLength": 5,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "language":
            {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
            }
    });
    if (current_edit) {
        t.row(current_edit).remove().draw();
    }

    var htmlActions = '<div class="text-center">' +
        '<button onclick="loadEdit(this)" class="btn btn-default"><span class="fa fa-edit"></span></button>' +
        '<button onclick="remove(this)" style="margin-left: 5px" class="remove btn btn-danger"><span class="fa fa-trash"></span></button>' +
        '</div>';

    if (relation_entity != false) {
        attr_type += '<div class="relation">Entity linked : ' + relation_entity + '</div>';
    }

    var row =
        [
            "<td class='text-center'><p class='text-center'>" + htmlActions + "</p></td>",
            "<td class='text-center'><p class='text-center space-top' >" + attr_name + "</p></td>",
            "<td class='text-center'><p class='text-center space-top' >" + attr_label + "</p></td>",
            "<td class='text-center'><p class='text-center space-top' >" + attr_type + "</p></td>",
            "<td class='text-center'><p class='text-center space-top' >" + can_be_listed + "</p></td>",
            "<td class='text-center'><p class='text-center space-top' >" + can_be_edited + "</p></td>",
            "<td class='text-center'><p class='text-center space-top' >" + can_be_showed + "</p></td>",
            "<td class='text-center'><p class='text-center space-top' >" + can_be_null + "</p></td>",
            "<td class='text-center'><p class='text-center space-top' >" + unique + "</p></td>",
            "<td class='text-center'><p class='text-center space-top' >" + order + "</p></td>"
        ];
    t.row.add(row);
    t.draw();
}

/**
 * This function remove current line of table
 * @param btn
 */
function remove(btn) {
    var t = $('#datatable').DataTable({
        "destroy": true,
        "paging": true,
        "lengthChange": false,
        "pageLength": 5,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "language":
            {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
            }
    });
    var tr = btn.closest('tr');
    removeReferer(tr.cells[1].textContent);
    t.row(tr).remove().draw();
}

function addReferrer(name) {
    var selectReferrer = document.getElementById('entity_def_referrer');
    for (i = selectReferrer.options.length - 1; i >= 0; i--) {
        selectReferrer.remove(i);
    }
    var myTab = document.getElementById('datatable');
    var opt = document.createElement('option');
    opt.appendChild(document.createTextNode('Id'));
    opt.value = 'Id';

    selectReferrer.appendChild(opt);
    for (i = 1; i < myTab.rows.length; i++) {
        var objCells = myTab.rows.item(i).cells;
        var name = objCells.item(1).textContent;
        opt = document.createElement('option');
        opt.appendChild(document.createTextNode(name));
        opt.value = name;
        selectReferrer.appendChild(opt);
    }
}

function removeReferer(name) {
    var selectReferrer = document.getElementById('entity_def_referrer');
    for (var i = 0; i < selectReferrer.length; i++) {
        if (selectReferrer.options[i].value == name) selectReferrer.remove(i);
    }
}

/**
 * This function edit current line of table
 * @param btn
 */
function loadEdit(btn) {
    var t = $('#datatable').DataTable({
        "destroy": true,
        "paging": true,
        "lengthChange": false,
        "pageLength": 5,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "language":
            {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
            }
    });
    var tr = btn.closest('tr');
    attr_name = tr.cells[1].textContent;
    attr_label = tr.cells[2].textContent;
    if (tr.cells[3].textContent.split('Entity linked : ').length > 0) {
        relation_entity = tr.cells[3].textContent.split('Entity linked : ')[1];
        attr_type = tr.cells[3].textContent.split('Entity linked : ')[0];
    } else {
        attr_type = tr.cells[3].textContent;
    }
    can_be_listed = tr.cells[4].textContent == 'true' || false;
    can_be_edited = tr.cells[5].textContent == 'true' || false;
    can_be_showed = tr.cells[6].textContent == 'true' || false;
    can_be_null = tr.cells[7].textContent  == 'true' || false;
    unique = tr.cells[8].textContent;
    order = tr.cells[9].textContent;

    // load data into forms

    document.getElementById('attribute_def_name').value = attr_name;
    document.getElementById('attribute_def_label').value = attr_label;
    document.getElementById('attribute_def_type').value = attr_type;
    document.getElementById('attribute_def_order').value = order;
    document.getElementById('attribute_def_unique').value = unique;
    if (relation_entity != "false") {
        document.getElementById('attribute_def_entity').value = relation_entity;
    } else {
        document.getElementById('attribute_def_entity').value = "";
    }
    document.getElementById('attribute_def_nullable').checked = can_be_null;
    document.getElementById('attribute_def_show').checked = can_be_showed;
    document.getElementById('attribute_def_list').checked = can_be_listed;
    document.getElementById('attribute_def_edit').checked = can_be_edited;

    current_edit = tr;
    checkType();
    $('#myModal').modal('show');
}