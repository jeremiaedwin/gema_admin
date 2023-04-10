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
        firestore.collection('majors').get().then((querySnapshot) => {
            querySnapshot.forEach((doc) => {
                const newRow = $('<tr>');
                // Add data to the row
                newRow.append($('<td>').text(no++));
                newRow.append($('<td>').text(doc.data().major_name));
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
                // Add data to the 'categories' collection
                firestore.collection('majors').add({
                    major_id: no,
                    major_name: majorName
                }).then((docRef) => {
                    console.log("Document written with ID: ", docRef.id);
                    // Reload the page to show the updated data
                    location.reload();
                })
                .catch((error) => {
                    console.error("Error adding document: ", error);
                });
            }
        });

    })
</script>

@endpush