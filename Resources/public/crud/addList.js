var class_name;
var nbElements = 0;
var elements_saved = 0;
var rows_saved = 0;
var parent_id;
var nb_rows = 0;
var element_rows = [];
var progress = 0;
var ind_progress = 0;
var nb_image = 0;
var nb_image_callback = 0;
var global_is_edit = false;
var current_row_edited;
var call_back_submit = false;
var call_back_error_validator = false;


(function () {
    initDataTables();
})();

/**
 *
 * @param className
 * @param path
 * @param callback
 */
function getFormModal(className, callback = false, is_edit = false) {
    global_is_edit = is_edit;
    if (!is_edit) {
        document.getElementById('loader-new').style = 'display:inline-block';
    }
    var path = document.getElementById('path-edit-modal').textContent;
    class_name = className;
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(xhttp.response);
            document.getElementById('modal-result').innerHTML = data.template;
            formInitJs();
            if (callback) callback();
            document.getElementById('loader-new').style = 'display:none';
            $('#myModal').modal('show');
        }
    };
    xhttp.open("GET", path, true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send();
}

function formInitJs() {
    tinymce.remove("#modal-result .tinymce");
    tinymce.init(
        {
            selector: "#modal-result .tinymce"
        }
    );
    $('#modal-result .datepicker').daterangepicker({
        singleDatePicker: true,
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Valider",
            "cancelLabel": "Annuler",
            "fromLabel": "De",
            "toLabel": "à",
            "customRangeLabel": "Custom",
            "daysOfWeek": [
                "Dim",
                "Lun",
                "Mar",
                "Mer",
                "Jeu",
                "Ven",
                "Sam"
            ],
            "monthNames": [
                "Janvier",
                "Février",
                "Mars",
                "Avril",
                "Mai",
                "Juin",
                "Juillet",
                "Août",
                "Septembre",
                "Octobre",
                "Novembre",
                "Décembre"
            ]
        }
    })
}


/**
 *
 * @param form
 * @param e
 */
function saveNew(form, e) {
    const capitalize = (s) => {
        if (typeof s !== 'string') return ''
        return s.charAt(0).toUpperCase() + s.slice(1)
    }
    e.preventDefault();
    validator(class_name, form, function () {
        var id = '#' + class_name + '-dataTable';
        var dataTable = $(id).DataTable({
            "destroy": true,
            "paging": true,
            "pageLength": 5,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language":
                {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
                }
        });
        var table = document.getElementById(class_name + "-dataTable");
        var tr = table.rows;
        var ths = table.getElementsByTagName("th");
        var data = [];
        var row = [];
        var label;
        var labels = document.getElementById('myModal').getElementsByTagName('LABEL');

        var htmlActions = '<div class="text-center">' +
            '<button type="button" onclick="loadModalEdit(\'' + class_name + '\',this)" class="btn btn-default"><span style="display: none" class="loader-edit spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="fa fa-edit"></span></button>' +
            '<button type="button" onclick="removeRow(\'' + class_name + '\',this)" style="margin-left: 5px" class="remove btn btn-danger"><span class="fa fa-trash"></span></button>' +
            '</div>';
        var row = ["<td class='text-center'> " + htmlActions + "</td>"];
        for (var i = 0; i < labels.length; i++) {
            if (labels[i].htmlFor != '') {
                var elem = document.getElementById(labels[i].htmlFor);
                if (elem) {
                    elem.label = labels[i];
                    label = elem.label.textContent;
                    var data = elem.value;
                    var is_file = false;
                    for (var j = 0; j < ths.length; j++) {
                        var is_img = false;
                        if (capitalize(ths[j].innerText.toLowerCase()) === label) {
                            switch (elem.getAttribute('data-type')) {
                                case "TinyMce":
                                    data = tinyMCE.get(elem.getAttribute('id')).save();
                                    break;
                                case "Simple select":
                                    data = elem.options[elem.selectedIndex].text;
                                    break;
                                case "Simple image picker":
                                    data = '<img style="width: 150px" id="preview-' + nb_image + '">';
                                    loadPreview(elem);
                                    nb_image++;
                                    break;
                            }
                            row.push("<td class='text-center'><p data-type='" + elem.getAttribute('data-type') + "' class='text-center space-top' >" + data + "</p></td>");
                        }
                    }
                }
            }
        }
        dataTable.row.add(row);
        dataTable.draw();
        if (global_is_edit) {
            removeRow(class_name, current_row_edited);
        }
        $('#myModal').modal('hide');

    });
}

/**
 *
 * @param e
 * @param form
 * @param parent_classname
 * @param path
 * @returns {boolean}
 */
function submitAddListsIfExist(e, form, parent_classname, path_parent, path_child) {
    resetVars();
    class_name = parent_classname;
    var stop  = true;
    var addLists = document.getElementsByClassName('add-list');
    var elements = [];
    if (addLists.length > 0) {
        e.preventDefault();
        for (var i = 0; i < addLists.length; i++) {
            var table_id = addLists[i].getElementsByClassName('datatable')[0].getAttribute('id');
            console.log(table_id);
            var table = $('#' + table_id).DataTable();
            var rows = table.$("tr");
            if (rows.length == 0)
            {
                stop = false;
                saveParent(parent_classname, path_parent, path_child, form, function (data) {
                    call_back_submit(data);
                });
            }
            element_rows.push([addLists[i].getElementsByClassName('datatable')[0]]);
            nbElements++;
            Object.size = function (obj) {
                var size = 0, key;
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) size++;
                }
                return size - 1;
            };
            var size = Object.size(rows);
            if (size > 0) {
                window.scrollTo(0, 0);
                for (var j = 0; size > j; j++) {
                    element_rows[i].push(rows[j]);
                    nb_rows++;
                }
            }
        }
        if (stop)
        {
            saveParent(parent_classname, path_parent, path_child, form, function () {
                document.getElementById('loader').style.display = 'block';
                saveElement(path_child);
            });
        }
    }
}

/**
 *
 * @param parent_classname
 * @param path
 * @param form
 * @param callback
 */
function saveParent(parent_classname, path_parent, path_child, form, callback) {
    parent_id = document.getElementById('form-row').getAttribute('data-id');
    if (parent_id === '') {
        parent_id = false;
    }
    var postData = {'classname': parent_classname, 'id': parent_id};
    var formElements = form.elements;
    for (var i = 0; i < formElements.length; i++) {
        if (formElements[i].type != "submit") //we dont want to include the submit-buttom
        {
            var label = formElements[i].name.split('crud_maker[');
            if (label.length > 1) {
                label = label[1].replace(']', '');
                if (label != '_token') postData[label] = formElements[i].value;
            }
        }
    }
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(xhttp.response);
            if (data.status == true) {
                parent_id = data.id;
                callback(data);
            } else {
                // keep old add Lists html
                var oldAddLists = [];
                var addLists = document.getElementsByClassName('add-list');
                for (let addList of addLists) {
                    oldAddLists.push(addList.innerHTML);
                }
                // print form errors
                document.getElementById('form-row').innerHTML = data.template;
                addLists = document.getElementsByClassName('add-list');

                // replace empty add list by old Add lists HTML
                ind = 0;
                for (let addList of addLists) {
                    addList.innerHTML = oldAddLists[ind];
                    ind++;
                }
                document.getElementById('loader').style.display = 'none';
                if (call_back_error_validator) {
                    call_back_error_validator();
                }
            }

        }
    };
    xhttp.open("POST", path_parent);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("data=" + JSON.stringify(postData));
}

function resetVars() {
    nbElements = 0;
    elements_saved = 0;
    rows_saved = 0;
    nb_rows = 0;
    element_rows = [];
    progress = 0;
    ind_progress = 0;
    nb_image = 0;
    nb_image_callback = 0;
    current_row_edited;
}


/**
 *
 * @param path_child
 */
function saveElement(path_child) {
    if (elements_saved < nbElements) {
        var table = element_rows[elements_saved][0];
        var child_classname = table.getAttribute('id').split('-dataTable')[0];
        var ths = table.getElementsByTagName("th");
        var row_datas = {};
        var ind = rows_saved + 1
        if (rows_saved == element_rows[elements_saved].length) {
            var ind = rows_saved;
        }
        for (var j = 1; j < ths.length; j++) {
            var cell_label = ths[j].textContent;
            var type = element_rows[elements_saved][ind].cells[j].getElementsByTagName('P')[0].getAttribute('data-type');
            var cell_data = element_rows[elements_saved][ind].cells[j].textContent;
            if (type == "Simple image picker") {
                cell_data = btoa(element_rows[elements_saved][ind].cells[j].getElementsByTagName('IMG')[0].getAttribute('src'));
            }
            row_datas[cell_label] = cell_data;
        }
        var postData = {
            'child_classname': child_classname,
            'id': parent_id,
            'parent_classname': class_name,
            'row_datas': row_datas
        };
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                rows_saved++;
                ind_progress++;
                progress = ind_progress / nb_rows * 100;
                document.getElementById('crud-progress-bar').style.width = progress.toFixed(2) + '%';
                document.getElementById('crud-progress-bar').textContent = progress.toFixed(2) + '%';
                if (nb_rows <= rows_saved) {
                    var data = JSON.parse(xhttp.response);
                    if (!call_back_submit) {
                        window.location.href = data['redirect_path'];
                    } else {
                        call_back_submit(data);
                    }
                    elements_saved++;
                    rows_saved = 0;
                }
                saveElement(path_child);
            }
        };
        xhttp.open("POST", path_child, true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send("data=" + JSON.stringify(postData));
    }
}

/**
 *
 * @param classname
 * @param btn
 */
function removeRow(classname, btn) {
    var id = '#' + classname + '-dataTable';
    var t = $(id).DataTable();
    var tr = btn.closest('tr');
    t.row(tr).remove().draw();
}

/**
 *
 * @param classname
 */
function loadModalEdit(classname, btn) {
    current_row_edited = btn;
    var tr = btn.closest('tr');
    var current = document.getElementById('current-' + classname).getAttribute('data-current');
    var loader = $(tr).find('.loader-edit');
    loader.show();
    getFormModal(classname, function () {
        var table = document.getElementById(classname + "-dataTable");
        var labels = document.getElementsByTagName('LABEL');
        var ths = table.getElementsByTagName("th");
        var td;
        for (var i = 0; i < labels.length; i++) {
            if (labels[i].htmlFor != '') {
                var elem = document.getElementById(labels[i].htmlFor);
                if (elem) {
                    elem.label = labels[i];
                    label = elem.label.textContent;
                    for (var j = 0; j < ths.length; j++) {
                        if (ths[j].textContent == label) {
                            var val = tr.cells[j].textContent.replace(/[\n\r]+|[\s]{2,}/g, ' ').trim(); // remove large white spaces
                            switch (elem.getAttribute('data-type')) {
                                case "Simple select":
                                    for (var k = 0; k < elem.options.length; k++) {
                                        if (elem.options[k].textContent === val) {
                                            elem.selectedIndex = k;
                                        }
                                    }
                                    break;
                                case "Simple image picker":
                                    var id_preview = tr.cells[j].getElementsByTagName('IMG')[0].getAttribute('id') + '-modal';
                                    var blob = tr.cells[j].getElementsByTagName('IMG')[0].getAttribute('src');
                                    var preview = document.createElement("img");
                                    var div = document.createElement("p");
                                    var wrapper = document.createElement("div");
                                    var content = document.createTextNode("Image actuelle : ");
                                    wrapper.classList.add('col-md-12');
                                    wrapper.classList.add('text-center');
                                    wrapper.style = "margin-top:10px";
                                    div.appendChild(content);
                                    preview.src = blob;
                                    preview.style = "margin: 0 15px !important;width:100px";
                                    wrapper.append(div);
                                    wrapper.append(preview);
                                    elem.after(wrapper);
                                    break;
                                default:
                                    elem.value = val;
                            }
                        }
                    }
                }
            }
        }
        loader.hide();
    }, true);
}


function initDataTables() {
    $('.datatable').DataTable({
        "destroy": true,
        "pageLength": 5,
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
}

/**
 *
 * @param input
 */
function loadPreview(input, callback) {
    var current_file = input.files[0];
    var reader = new FileReader();
    reader.onload = function (event) {
        var image = new Image();
        image.src = event.target.result;
        image.onload = function () {
            var maxWidth = 800,
                maxHeight = 800,
                imageWidth = image.width,
                imageHeight = image.height;
            if (imageWidth > imageHeight) {
                if (imageWidth > maxWidth) {
                    imageHeight *= maxWidth / imageWidth;
                    imageWidth = maxWidth;
                }
            } else {
                if (imageHeight > maxHeight) {
                    imageWidth *= maxHeight / imageHeight;
                    imageHeight = maxHeight;
                }
            }
            var canvas = document.createElement('canvas');
            canvas.width = imageWidth;
            canvas.height = imageHeight;
            image.width = imageWidth;
            image.height = imageHeight;
            var ctx = canvas.getContext("2d");
            ctx.drawImage(this, 0, 0, imageWidth, imageHeight);
            canvas.toDataURL(current_file.type);
            $('#preview-' + nb_image_callback).attr('src', canvas.toDataURL(current_file.type));
            nb_image_callback++;
        }
    };
    reader.readAsDataURL(current_file);
}
