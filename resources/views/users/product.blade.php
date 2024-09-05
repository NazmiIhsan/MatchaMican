@extends('layouts.app')

@section('content')
    <style>
        .box__dragndrop,
        .box__uploading,
        .box__success,
        .box__error {
            display: none;
        }

        .box.has-advanced-upload {
            background-color: white;
            outline: 2px dashed black;
            outline-offset: -10px;
        }

        .box.has-advanced-upload .box__dragndrop {
            display: inline;
        }
    </style>

    <head>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
            integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>



        {{-- DropZone --}}
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />

        {{-- *DataTables --}}
        <link href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">

    </head>
    <div class="card">
        <div class="card-header">
            <h5>List Of Product</h5>
        </div>

        <div class="card-body">
            <div style="float: right">
                <button class="form form-control btn-primary" id="fq" data-toggle="modal" data-target="#myModal"><i
                        class="fas fa-plus"></i> Add Product</button>

            </div>

            {{-- Add Modal --}}
            <div class="modal fade" id="myModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Add Product</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <!-- Modal body -->
                        <form action="{{ url('insertproduct') }}" method="POST" id="add_product"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Name">
                                <br>
                                <label for="price">Price:</label>
                                <input type="number" id="price" name="price" class="form-control"
                                    placeholder="Price">
                                <br>
                                <label for="Image">Image:</label>
                                <input type="file" accept="image/png, image/gif, image/jpeg" id="img"
                                    name="img" class="form-control" placeholder="Image">
                                <br>
                                <div class="form-group">
                                    <img id="preview" src="#" alt="Image Preview"
                                        style="display:none; max-width:200px; height:auto;">
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-success">Add</button>
                            </div>
                        </form>
                    </div>
                    <script>
                        document.getElementById('img').addEventListener('change', function(event) {
                            var reader = new FileReader();
                            reader.onload = function() {
                                var output = document.getElementById('preview');
                                output.src = reader.result;
                                output.style.display = 'block';
                            }
                            if (event.target.files.length > 0) {
                                reader.readAsDataURL(event.target.files[0]);
                            }
                        });
                    </script>

                </div>
            </div>

            {{-- Edit Modal --}}
            <div class="modal fade" id="Editmodal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Product</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <!-- Modal body -->

                        <div class="modal-body">
                            <input type="hidden" id="id" name="id">
                            <label for="name">Name:</label>
                            <input type="text" id="named" name="named" class="form-control" placeholder="Name">
                            <br>
                            <label for="price">Price:</label>
                            <input type="number" id="priced" name="price" class="form-control" placeholder="Price">
                            <br>
                            <label for="Image">Image:</label>
                            <input type="file" accept="image/png, image/gif, image/jpeg" id="imged" name="img"
                                class="form-control" placeholder="Image">

                            <br>
                            <img id="imgPreview" src="#" alt="Image Preview"
                                style="max-width: 100px; max-height: 100px; display: none;">


                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success" onclick="update(this)">Update</button>
                        </div>
                    </div>
                </div>
            </div>
            @php
                $i = 1;
            @endphp
            <div class="table-responsive">

                <table class="table table-striped" id="myTable">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Price</th>
                            <th scope="col">Image</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($get_data as $item)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->price }}</td>
                                <td>
                                    {{-- @dump(asset( $item->image), $item->image_path) --}}
                                    @if ($item->image)
                                        <img src="{{ asset($item->image) }}" alt="Item Image"
                                            style="max-width: 100px; max-height: 100px;">
                                    @else
                                        No Image Available
                                    @endif


                                </td>
                                <td>
                                    <button class="btn btn-danger" id="delete"
                                        onclick="deleteProduct({{ $item->id }})">Delete</button>

                                    <button class="btn btn-warning" id="edit" data-id="{{ $item->id }}"
                                        data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                                        data-image="{{ $item->image }}" onclick="editProduct(this)">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    No Product Available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <script>
                function deleteProduct(tht) {
                    console.log(tht);

                    swal({
                            title: "Are you sure?",
                            text: "Once deleted, you will not be able to recover this imaginary file!",
                            icon: "warning",
                            buttons: true,
                            buttons: ['No', 'Yes'],
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete) {


                                $.ajax({
                                    type: "DELETE",
                                    url: "{{ url('deleteproduct') }}/" + tht,
                                    data: {
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(response) {
                                        swal("Product Deleted Successfully!", {
                                            icon: "success",

                                        }).then(() => {
                                            location.reload(); // Reload the page after the alert is closed
                                        });
                                    }
                                });

                            }
                        });


                }



                //  Edit Product

                function editProduct(tht) {
                    // console.log(tht);
                    let id = $(tht).data('id');
                    let name = $(tht).data('name');
                    let price = $(tht).data('price');
                    let img = $(tht).data('image');

                    console.log(id, name, price, img);

                    $('#Editmodal').modal('show');


                    $('#id').val(id);
                    $('#named').val(name);
                    $('#priced').val(price);

                    if (img) {
                        $('#imgPreview').attr('src', img).show();
                        console.log('masuk');
                    } else {
                        $('#imgPreview').hide();
                        console.log('tak masuk');

                    }




                    // update(id);.


                }


                function update(tht, id) {

                    let idz = $('#id').val();
                    let named = $('#named').val();
                    let priced = $('#priced').val();
                    let img = $('#imged').val();


                    console.log(idz, named, priced, img);
                    swal({
                        title: "Are you sure?",
                        icon: "warning",
                        buttons: true,
                        buttons: ['No', 'Yes'],
                        // dangerMode: true,
                    }).then((willUpdate) => {
                        if (willUpdate) {
                            $.ajax({
                                method: "PUT",
                                url: "{{ url('updateproduct') }}/" + idz,
                                data: {
                                    id: idz,
                                    name: named,
                                    price: priced,
                                    // image: img,
                                    _token: "{{ csrf_token() }}"
                                },

                            })
                        }
                    })


                }
            </script>
            @if (Session::has('message'))
                {
                <script>
                    // swal("Message","{{ Session::get('message') }}","success"),{
                    //     button:"true",
                    //     button:"OK",
                    // }

                    swal({
                        title: "{{ Session::get('message') }}",
                        // text: "You clicked the button!",
                        icon: "success",
                        button: "OK",
                    });
                </script>
                }
            @endif
        </div>

    </div>
@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    <script>
        $(document).ready(function() {


            var test = 'lala';

            console.log(test);

            $("#fq").on("click", function() {
                console.log('Button clicked');
                alert("Handler for `click` called.");
            });


        });
    </script>
@endpush
