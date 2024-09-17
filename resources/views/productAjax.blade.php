<!DOCTYPE html>
<html>
<head>
    <title>AJAX CRUD FOR PRODUCTS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css"/>
</head>
<body>
<div class="container">
    <h1>Ajax CRUD App For Products</h1>
    <a href="javascript:void(0)" class="btn btn-info mb-3" id="createNewProduct">Add Product</a>
    <table class="table table-bordered" id="productTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<div class="modal fade" id="ajaxModelexa" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalHeading">Add Product</h4>
            </div>
            <div class="modal-body">
                <form name="productForm" id="productForm" class="form-horizontal">
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                      <label for="title" class="col-sm-2 control-label">Title</label>
                      <div class="col-sm-12">
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title" >
                        <span id="title-error" class="text-danger"></span>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="description" class="col-sm-2 control-label">Description</label>
                      <div class="col-sm-12">
                        <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description" >
                        <span id="description-error" class="text-danger"></span>
                      </div>
                     
                    </div>
                    <div class="col-sm-offset-2 col-sm-10">
                     <button type="submit" class="btn btn-primary" id="savedata">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function(){
    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function fetchProducts(){
        $.ajax({
            url: "{{ route('products.index') }}",
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                let rows = '';
                $.each(data, function(key, product) {
                    rows += `<tr>
                        <td>${product.id}</td>
                        <td>${product.title}</td>
                        <td>${product.description}</td>
                        <td>
                            <button class="btn btn-info btn-sm editProduct" data-id="${product.id}">Edit</button>
                            <button class="btn btn-danger btn-sm deleteProduct" data-id="${product.id}">Delete</button>
                        </td>
                    </tr>`;
                });
                $('#productTable tbody').html(rows);
            }
        });
    }

    fetchProducts(); 

    $('#createNewProduct').click(function(){
        $('#savedata').val("create-product");
        $('#id').val('');
        $('#productForm').trigger("reset");
        $('#modalHeading').html("Create New Product");
        $('#ajaxModelexa').modal('show');
    });

    $('body').on('click', '.editProduct', function(){
    var id = $(this).data('id');
    $.get("{{ route('products.edit', ['product' => 'id']) }}".replace('id', id), function(data){
        $('#modalHeading').html('Edit Product');
        $('#savedata').val('edit-product');
        $('#ajaxModelexa').modal('show');
        $('#id').val(data.id);
        $('#title').val(data.title);
        $('#description').val(data.description);
    });
});


    $('#productForm').submit(function(e){
    e.preventDefault();
    var formData = $(this).serialize();
    var url = $('#savedata').val() === 'create-product' ? "{{ route('products.store') }}" : "{{ route('products.store', ['product' => 'id']) }}";
    
    
    if ($('#savedata').val() !== 'create-product') {
        url = url.replace('id', $('#id').val()); 
    }
    
    $.ajax({
        url: url,
        type: 'post',
        data: formData,
        dataType: 'json',
        success: function(data){
            $('#productForm').trigger('reset');
            $('#ajaxModelexa').modal('hide');
            fetchProducts();
            $('.is-invalid').removeClass('is-invalid');
        },
        error: function(response) {
        var errors = response.responseJSON.errors;
        $.each(errors, function(key, value) {
            $('#' + key).addClass('is-invalid');
            $('#' + key + '-error').text(value[0]);
        });
    }
    });
});


    $('body').on('click', '.deleteProduct', function(){
        if(confirm("Are you sure you want to delete this product?")) {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('products.index') }}/" + id,
                type: 'DELETE',
                success: function(data) {
                    fetchProducts();
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        }
    });
});
</script>
</body>
</html>
