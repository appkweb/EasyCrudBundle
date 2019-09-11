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
var size;
var can_be_null;
var can_be_visible;
var current_edit = false;

// EXECUTED ON PAGE LOAD
(function () {
})();

/**
 *
 */
function checkType() {
    size = true;
    relation_entity = true;
    attr_type = document.getElementById('attribute_def_type').value;
    switch (true) {
        case attr_type == "string" || attr_type == "text":
            document.getElementById('entity-container').style.display = "none";
            document.getElementById('size-container').style.display = "block";
            relation_entity = "false";
            break;
        case attr_type == "integer":
            document.getElementById('size-container').style.display = "none";
            document.getElementById('entity-container').style.display = "none";
            size = "false";
            relation_entity = "false";
            break;
        case attr_type == "ManyToOne" || attr_type == "OneToMany" || attr_type == "OneToOne":
            size = "false";
            document.getElementById('entity-container').style.display = "block";
            document.getElementById('size-container').style.display = "none";
            break;
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
    can_be_null = document.getElementById('attribute_def_nullable').checked;
    can_be_visible = document.getElementById('attribute_def_visible').checked;
}

/**
 * This function was used on click event of "Add attribute" button to know if it's an edit or new attr
 */
function newAttr() {
    clear();
    current_edit = false;
}

/**
 * This function reset value of attribute's form
 */
function clear() {
    document.getElementById('attribute_def_name').value = '';
    document.getElementById('attribute_def_label').value = '';
    document.getElementById('attribute_def_type').value = 'integer';
    document.getElementById('attribute_def_order').value = '';
    document.getElementById('attribute_def_length').value = '';
    document.getElementById('attribute_def_entity').value = '';
    document.getElementById('attribute_def_nullable').checked = false;
    document.getElementById('attribute_def_visible').checked = false;
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

    if (relation_entity != "false") {
        relation_entity = document.getElementById('attribute_def_entity').value;
    }
    if (size != "false") {
        size = document.getElementById('attribute_def_length').value;
    }

    var row =
        [
            "<td class='text-center'><p class='text-center'>" + htmlActions + "</p></td>",
            "<td class='text-center'><p class='text-center space-top name' >" + attr_name + "</p></td>",
            "<td class='text-center'><p class='text-center space-top date' >" + attr_label + "</p></td>",
            "<td class='text-center'><p class='text-center space-top date' >" + attr_type + "</p></td>",
            "<td class='text-center'><p class='text-center space-top date' >" + relation_entity + "</p></td>",
            "<td class='text-center'><p class='text-center space-top date' >" + size + "</p></td>",
            "<td class='text-center'><p class='text-center space-top date' >" + can_be_null + "</p></td>",
            "<td class='text-center'><p class='text-center space-top date' >" + can_be_visible + "</p></td>",
            "<td class='text-center'><p class='text-center space-top date' >" + order + "</p></td>"
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
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "language":
            {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
            }
    });
    var tr = btn.closest('tr');
    t.row(tr).remove().draw();
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
        "searching": false,
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
    attr_type = tr.cells[3].textContent;
    relation_entity = tr.cells[4].textContent;
    size = tr.cells[5].textContent;
    can_be_null = tr.cells[6].textContent == 'true' || false;
    can_be_visible = tr.cells[7].textContent == 'true' || false;
    order = tr.cells[8].textContent;
    // load data into forms

    document.getElementById('attribute_def_name').value = attr_name;
    document.getElementById('attribute_def_label').value = attr_label;
    document.getElementById('attribute_def_type').value = attr_type;
    document.getElementById('attribute_def_order').value = order;
    if (size != "false") {
        document.getElementById('attribute_def_length').value = size;
    } else {
        document.getElementById('attribute_def_length').value = "";
    }
    if (relation_entity != "false") {
        document.getElementById('attribute_def_entity').value = relation_entity;
    } else {
        document.getElementById('attribute_def_entity').value = "";
    }
    document.getElementById('attribute_def_nullable').checked = can_be_null;
    document.getElementById('attribute_def_visible').checked = can_be_visible;

    current_edit = tr;
    checkType();
    $('#myModal').modal('show');
}