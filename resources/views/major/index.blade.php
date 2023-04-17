@extends('layouts.admin')

@section('main-content')

<div class="card">
    <div class="card-body">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">
        Add Data
        </button>

        <table id="majorsTable" class="table" border="1">
            <thead>
                <tr>
                <th>No</th>
                <th>Major Name</th>
                <th>Action</th>
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
                <h1 class="modal-title fs-5" id="createModalLabel">Create New Major</h1>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="major_name">Category Name</label>
                    <input type="text" name="major_name" id="major_name" class="form-control">
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
                    <h1 class="modal-title fs-5" id="editModalLabel">Edit Major</h1>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_major_name">Major Name</label>
                        <input type="text" name="edit_major_name" id="edit_major_name" class="form-control">
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
        const majorsTable = $('#majorsTable tbody');
        var no = 1;
        firestore.collection('majors').orderBy("major_id", "asc").get().then((querySnapshot) => {
            querySnapshot.forEach((doc) => {
                const newRow = $('<tr>');
                // Add data to the row
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(doc.data().major_name));
                const deleteButton = $('<button>').attr('id', `delete-major-${doc.id}`).addClass('btn btn-danger btn-sm delete-button').text('Delete');
                newRow.append($('<td>').append(deleteButton));
                const editButton = $('<button>').attr('id', `edit-major-${doc.id}`).addClass('btn btn-warning btn-sm edit-button').text('Edit');
                newRow.append($('<td>').append(editButton));
                // Append the row to the table
                majorsTable.append(newRow);
            });
        }).catch((error) => {
            console.error(error);
        });


        // Add event listener to the save button in the modal
        $('#createModal button.btn-primary').on('click', function(){
            const majorName = $('#major_name').val();
            
            if(majorName){
                // Select the last row of 'majors' collection
                firestore.collection('majors').orderBy("major_id", "desc").limit(1).get()
                .then((querySnapshot) => {
                    let lastMajorId = 0;
                    if (!querySnapshot.empty) {
                        querySnapshot.forEach((doc) => {
                            lastMajorId = doc.data().major_id;
                        });
                    }
                    const newMajorId = lastMajorId + 1;
                    // Add data to the 'majors' collection with the new auto-incremented major_id
                    firestore.collection('majors').doc(newMajorId.toString()).set({
                        major_id: newMajorId.toString(),
                        major_name: majorName
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

        // Delete function
        majorsTable.on('click', '.delete-button', function() {
            const buttonId = $(this).attr('id');
            const majorId = buttonId.substring(13);
            firestore.collection('majors').doc(majorId).delete().then((docRef) => {
                // Reload the page to show the updated data
                location.reload();
            })
        });

        majorsTable.on('click', '.edit-button', function() {
            const buttonId = $(this).attr('id');
            const majorId = buttonId.substring(11);
            const majorName = $(this).closest('tr').find('td:eq(1)').text();
            $('#edit_major_name').val(majorName);
            $('#saveChanges').attr('data-major-id', majorId);
            $('#editModal').modal('show');
        });

        $('#saveChanges').on('click', function(){
        const majorId = $(this).attr('data-major-id');
        const majorName = $('#edit_major_name').val();
        
        if(majorName){
            // Update data in the 'majors' collection
            firestore.collection('majors').doc(majorId).update({
                major_name: majorName
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