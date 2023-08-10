<script src="<?php echo base_url('public/ckeditor/ckeditor.js'); ?>"></script>

<div class="container pb-5" style="background: #ecf0f5; ">

  <form class="mt-5" method="POST" enctype="multipart/form-data">
    <div class="text-center">
      <h2>Thông Tin Sản Phẩm</h2>
    </div>
    <div class="text-center">
      <?php if(isset($error)){ ?>
        <span><?php echo $error; ?></span>
      <?php } ?>
      <?php if(isset($success)){ ?>
        <span style="color: #0f9ed8"><?php echo $success; ?></span>
        <br>
      <?php } ?>
    </div>
    <div class="row d-flex justify-content-between mb-3 ">
      <div class="col-md-7">
        <label for="tsp" class="form-label">Tên sản phẩm <span>(*)</span> </label>
        <input type="text" class="form-control" id="tsp" name="name" required>
      </div>
      <div class="col-md-4">
        <label for="gg" class="form-label">Giá gốc <span>(*)</span></label>
        <input type="number" class="form-control" id="gg" min="0" value="0" name="price_root" required>
      </div>
    </div>

    <div class="row d-flex justify-content-between mb-3 ">
      <div class="col-md-7">
        <div class="col-12 d-flex justify-content-between">
          <div class="col-md-6">
            <label for="lsp" class="form-label">Loại sản phẩm <span>(*)</span> </label>
            <select class="form-select form-control" id="lsp" name="catid">
              <?php foreach ($category as $key => $value): ?>
                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="col-md-5">
            <label for="sl" class="form-label">Số lượng<span>(*)</span> </label>
            <input type="number" min="1" value="1" class="form-control" id="sl" name="number" required>
          </div>
        </div>  
      </div>
      <div class="col-md-4">
        <label for="gb" class="form-label">Giá bán <span>(*)</span></label>
        <input type="number" class="form-control" id="gb" min="0" value="0" name="price_buy" required>
      </div>
    </div>

    <div class="row d-flex justify-content-between mb-3 ">
      <div class="col-md-7">
        <label for="tsp" class="form-label">Mô tả ngắn <span>(*)</span> </label>
        <textarea class="form-control" rows="3" name="sortDesc" required></textarea>
      </div>
      <div class="col-md-4">
        <label for="km" class="form-label">Khuyến mãi <span>(*)</span></label>
        <input type="text" class="form-control" id="km" value="0%" disabled required>
      </div>
    </div>

    <div class="row d-flex justify-content-between mb-3 ">
      <div class="col-md-7">
        <label for="detail" class="form-label">Mô tả chi tiết <span>(*)</span></label>
        <textarea id="detail" class="form-control" name="detail" required></textarea>
        <script> CKEDITOR.replace('detail', { height: '500px' }); </script>
      </div>
      <div class="col-md-4">
        <div class="row">
          <div class="col-md-12 mb-3">
              <label for="anhchinh" class="form-label">Ảnh chính <span>(*)</span></label>
              <input type="file" id="anhchinh" class="form-control" name="avatar">
          </div>

          <div class="col-md-12">
              <label for="anhphu" class="form-label">Ảnh sản phẩm<span>(*)</span></label>
              <input type="file" id="anhphu" class="form-control" name="images[]" multiple>
          </div>
        </div>
      </div>
    </div>
    <div class="text-right">
      <a href="<?php echo base_url('thong-tin-khach-hang/'); ?>" class="btn btn-secondary">Quay Lại</a>
      <button type="submit" class="btn btn-primary">Đăng Sản Phẩm</button>
    </div>
  </form>
</div>

<style type="text/css">
  .form-control:focus {
    box-shadow: none;
  }

  .form-control:disabled{
    background: white;
  }

  .form-control{
    border-radius: 0px;
    font-size: 15px;
  }

  label{
    font-size: 14px;
  }

  span{
    color: red;
  }

  .btn{
    padding-left: 15px;
    padding-right: 15px;
    padding-top: 5px;
    padding-bottom: 5px;
    font-size: 15px;
  }

  .d-flex{
    display: flex;
  }

  .justify-content-between{
    justify-content: space-between;
  }

  .mb-3{
    margin-bottom: 16px;
  }

  .pb-5{
    padding-bottom: 48px;
  }

  .mt-5{
    margin-top: 48px;
  }

  .btn-secondary{
    background: #6c757d;
    color: white;
  }

  .btn-secondary:hover{
    color: white;
  }

  strong{
    color: black;
  }

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('#gb').on('input',function(e){
      let gg = $("#gg").val()
      if(gg != 0 && $(this).val() != '' && $(this).val() != 0){
        let km = parseInt(((parseInt(gg) - parseInt($(this).val())) / parseInt(gg)) * 100);
        $('#km').val(km + "%")
      }
    });
  });
</script>