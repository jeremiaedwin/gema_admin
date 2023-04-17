@extends('layouts.admin')

@section('main-content')

<div class="card">
    <div class="card-body">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createModal">
        Add Data
        </button>

        <table id="programTable" class="table" border="1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Major Name</th>
                    <th>Prodi Name</th>
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

        // Get data from the 'program studies' collection
        const programTable = $('#programTable tbody');
        var no = 1;
        const majorCollection = firestore.collection('majors');
        const programCollection = firestore.collection('study_programs');
        majorCollection.get().then((majorSnapshot) => {
            majorSnapshot.forEach((majorDoc) => {
                console.log(majorDoc.data().major_id);
                programCollection.where('major_id', '==', majorDoc.data().major_id).get().then((programSnapshot) => {
                    programSnapshot.forEach((programDoc) => {
                        const newRow = $('<tr>');
                        // Add data to the row
                        newRow.append($('<td>').text(no++));
                        newRow.append($('<td>').text(majorDoc.data().major_name));
                        newRow.append($('<td>').text(programDoc.data().study_program_name));
                        // Append the row to the table
                        programTable.append(newRow);
                    });
                });
            });
        }).catch((error) => {
            console.error(error);
        });

        // Add event listener to the save button in the modal
        $('#createModal button.btn-primary').on('click', function(){
            const majorName = $('#major_name').val();
            
            if(majorName){
                firestore.collection('majors').doc(no).set({
                    major_id: no,
                    major_name: majorName
                }).then(() => {
                    console.log("Document written with ID: ", no);
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