@extends('layouts.admin')

@section('main-content')

<div class="card">
    <div class="card-body">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">
        Add Data
        </button>

        <table id="categoriesTable" class="table" border="1">
            <thead>
                <tr>
                <th>No</th>
                <th>Category Type</th>
                <th>Category Name</th>
                <th>Aksi</th>
                <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="createModalLabel">Create New Category</h1>
            </div>
            <div class="modal-body">
            <div class="form-group">
                    <label for="category_type">Category Type</label>
                    <select name="category_type" id="category_type" class="form-control">
                        <option value="">Pilih Type</option>
                        <option value="1">Barang</option>
                        <option value="2">Jasa</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="category_name">Category Name</label>
                    <input type="text" name="category_name" id="category_name" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Category</h1>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                    <label for="edit_category_name">Category Type</label>
                        <select name="category_type" id="edit_category_type" class="form-control">
                            <option value="">Pilih Type</option>
                            <option value="1">Barang</option>
                            <option value="2">Jasa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_category_name">Category Name</label>
                        <input type="text" name="edit_category_name" id="edit_category_name" class="form-control">
                        <input type="hidden" name="edit_category_name" id="edit_category_id" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>

</div>



@endsection

@push('script')

<script>
    const myModal = document.getElementById('createModal')
    const myInput = document.getElementById('myInput')

        myModal.addEventListener('shown.bs.modal', () => {
        myInput.focus()
    })
</script>
<script  type="module">
    $(document).ready(function(){

        const firebaseConfig = {
            apiKey: "AIzaSyD9jXcNqK_lfSvsf1Vv3RN9_tJp17CLjek",
            authDomain: "geraimahasiswa-8d67b.firebaseapp.com",
            databaseURL: "https://geraimahasiswa-8d67b-default-rtdb.asia-southeast1.firebasedatabase.app",
            projectId: "geraimahasiswa-8d67b",
            storageBucket: "geraimahasiswa-8d67b.appspot.com",
            messagingSenderId: "548929566086",
            appId: "1:548929566086:web:4fc78131a731fdff422807",
            measurementId: "G-05D9ZJRYZ3"
        };

        firebase.initializeApp(firebaseConfig);

        // Get a reference to the firestore service
        const firestore = firebase.firestore();

        // Get data from the 'categories' collection
        const categoriesTable = $('#categoriesTable tbody');
        var no = 1;
        firestore.collection('categories').get().then((querySnapshot) => {
            querySnapshot.forEach((doc) => {
                const newRow = $('<tr>');
                // Add data to the row
                newRow.append($('<td>').text(no++));
                if(doc.data().ad_type_id == "2"){
                    newRow.append($('<td>').text("Jasa"));
                }else{
                    newRow.append($('<td>').text("Barang"));

                }
                
                newRow.append($('<td>').text(doc.data().category_name));
                const deleteButton = $('<button>').attr('id', `delete-category-${doc.id}`).addClass('btn btn-danger btn-sm delete-button').text('Delete');
                newRow.append($('<td>').append(deleteButton));
                const editButton = $('<button>').attr('id', `edit-category-${doc.id}`).addClass('btn btn-warning btn-sm edit-button').text('Edit');
                newRow.append($('<td>').append(editButton));

                // Append the row to the table
                categoriesTable.append(newRow);
            });
        }).catch((error) => {
            console.error(error);
        });


        // Add event listener to the save button in the modal
        $('#createModal button.btn-primary').on('click', function(){
            const categoryType = $('#category_type').val();
            const categoryName = $('#category_name').val();
            
            if(categoryType && categoryName){
                // Add data to the 'categories' collection
                // Select the last row of 'categories' collection
                firestore.collection('categories').orderBy("category_id", "desc").limit(1).get()
                .then((querySnapshot) => {
                    let lastCategoryId = 0;
                    if (!querySnapshot.empty) {
                        querySnapshot.forEach((doc) => {
                            lastCategoryId = doc.data().category_id;
                        });
                    }
                    const newCategoryId = parseInt(lastCategoryId) + 1;
                    // Add data to the 'categories' collection with the new auto-incremented category_id
                    firestore.collection('categories').doc(newCategoryId.toString()).set({
                        category_id: newCategoryId.toString(),
                        category_name: categoryName,
                        ad_type_id: categoryType
                    }).then((docRef) => {
                        // Reload the page to show the updated data
                        location.reload();
                    })
                    .catch((error) => {
                        console.error("Error adding document: ", error);
                    });
                })
                .catch((error) => {
                    console.error("Error getting documents: ", error);
                });
            }
        });

        // delete function
        categoriesTable.on('click', '.delete-button', function() {
            const buttonId = $(this).attr('id');
            const categoryId = buttonId.substring(13);
            firestore.collection('categories').doc(categoryId).delete().then((docRef) => {
                // Reload the page to show the updated data
                location.reload();
            })
        });

        categoriesTable.on('click', '.edit-button', function() {
            const buttonId = $(this).attr('id');
            const categoryId = buttonId.substring(14);
            const adType = $(this).closest('tr').find('td:eq(1)').text();
            const categoryName = $(this).closest('tr').find('td:eq(2)').text();
            console.log(categoryName);
            $('#edit_category_name').val(categoryName);
            $('#edit_category_id').val(categoryId);

            const adTypeId = adType; // change this to the actual ad type ID
    
            // set the selected option based on the ad type ID
            if (adTypeId === "Barang") {
                $('#edit_category_type').val('1');
            } else if (adTypeId === "Jasa") {
                $('#edit_category_type').val('2');
            }
            
            $('#editModal').modal('show');

            $('#saveChanges').attr('data-category-id', categoryId);
            $('#editModal').modal('show');
        });

        $('#saveChanges').on('click', function(){
        const categoryId = $('#edit_category_id').val();
        const categoryName = $('#edit_category_name').val();
        const adTypeId= $('#edit_category_type').val();
        console.log(categoryId)
        if(categoryName){
            // Update data in the 'categorys' collection
            firestore.collection('categories').doc(categoryId).update({

                    ad_type_id : adTypeId,
                    category_name: categoryName
                
            }).then((docRef) => {
                // Reload the page to show the updated data
                location.reload();
            })
            .catch((error) => {
                console.error("Error updating document: ", error);
            });
        }
    });

    })
</script>

@endpush