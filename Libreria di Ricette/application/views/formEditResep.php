<div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner" style="height: 400px">
    <div class="carousel-item active">
      <img src="<?= base_url("assets/img/").$resep['resepPic'] ?>" class="d-block w-100">
    </div>
  </div>
</div>

<!-- form input resep -->
    <div class="container align-items-center" style="margin-bottom: 75px">
        <form action="<?= site_url('RecipeController/create_recipe') ?>" method="post">
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" class="form-control" name="judul" placeholder="Masukkan teks" value="<?= $resep['judul'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" rows="5" placeholder="Masukkan teks" value="<?= $resep['deskripsi'] ?>" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Bahan - Bahan</label>
            </div>

            <div class="d-grid gap-3 field_wrapper">
                <?php
                $count = 0;
                foreach ($bahan_resep as $br) {
                    $count++;
                ?>
                    <div class="row g-3">
                        <div class="col-sm-7">
                            <select class="form-select" aria-label="Default select example" name="bahan[]" required>
                                <option>Pilih bahan</option>
                                <?php foreach ($bahan as $index => $b) {
                                    if ($b['idBahan'] == $bahan_resep[$index]['idBahan']) {?>
                                        <option selected value="<?= $b['idBahan'] ?>"><?= $b['namaBahan'] ?></option>
                                <?php } else { ?>
                                        <option value="<?= $b['idBahan'] ?>"><?= $b['namaBahan'] ?></option>
                                <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="col-sm">
                            <input type="text" name="takaran[]" class="form-control" placeholder="Takaran" aria-label="Takaran" value="<?= $bahan_resep[$index]['takaran'] ?>" required>
                        </div>
                        <?php if ($count == 1) { ?>
                            <a href="javascript:void(0);" class="add_button" title="Add bahan"><img height="30" src="<?= base_url('assets/img/add-icon.png') ?>"/></a>
                        <?php } else { ?>
                            <a href="javascript:void(0);" class="remove_button"><img height="30" src="<?= base_url('assets/img/remove-icon.png') ?>"/></a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <div class="mb-3" style="margin-top: 15px;">
                <label class="form-label">Langkah Pembuatan</label>
            </div>
            <?php 
            $count_langkah = 0;
            foreach ($langkah as $l) { 
                $count_langkah++;
            ?>
                <div class="mb-3 field_wrapper_langkah">
                    <input type="text" name="langkah[]" class="form-control" placeholder="Langkah" aria-label="Langkah" value="<?= $l['deskripsi'] ?>" required>
                    <?php if ($count_langkah == 1) { ?>
                        <a href="javascript:void(0);" class="add_button_langkah" title="Add langkah"><img height="30" src="<?= base_url('assets/img/add-icon.png') ?>"/></a>
                    <?php } else { ?>
                        <a href="javascript:void(0);" class="remove_button_langkah"><img height="30" src="<?= base_url('assets/img/remove-icon.png') ?>"/></a>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="col text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
<!-- end form input resep -->


<!-- additional script -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
    var maxField = 10; //Input fields increment limitation
    var maxFieldLangkah = 10;
    var addButton = $('.add_button'); //Add button selector
    var addButtonLangkah = $('.add_button_langkah');
    var wrapper = $('.field_wrapper'); //Input field wrapper
    var wrapperLangkah = $('.field_wrapper_langkah');
    var fieldHTML = '<div class="row g-3">' +
        '<div class="col-sm-7">' +
            '<select class="form-select" aria-label="Default select example" name="bahan[]" required>' +
                '<option selected>Pilih bahan</option>' +
                '<code><?php foreach ($bahan as $b) { ?></code>' +
                    '<option value="' +
                    '<?= $b['idBahan'] ?>' +
                    '"><code><?= $b['namaBahan'] ?></code></option>' +
                '<code><?php } ?></code></select></div><div class="col-sm"><input type="text" name="takaran[]" class="form-control" placeholder="Takaran" aria-label="Takaran" required></div><a href="javascript:void(0);" class="remove_button">' +
                '<img height="30" src="' +
                '<?= base_url('assets/img/remove-icon.png') ?>' +
                '"/></a></div>'; //New input field html 
    
    var fieldHTMLLangkah = '<div class="mb-3 field_wrapper_langkah"><input type="text" name="langkah[]" class="form-control" placeholder="Langkah" aria-label="Langkah" required><a href="javascript:void(0);" class="remove_button_langkah">' +
        '<img height="30" src="' +
        '<?= base_url('assets/img/remove-icon.png') ?>' +
        '"/></a></div>';

    var x = <?= $count ?>; //Initial field counter is 1
    var y = <?= $count_langkah ?>;
    
    //Once add button is clicked
    $(addButton).click(function(){
        //Check maximum number of input fields
        if(x < maxField){ 
            x++; //Increment field counter
            $(wrapper).append(fieldHTML); //Add field html
        }
    });

    $(addButtonLangkah).click(function(){
        //Check maximum number of input fields
        if(y < maxFieldLangkah){ 
            y++; //Increment field counter
            $(wrapperLangkah).append(fieldHTMLLangkah); //Add field html
        }
    });
    
    //Once remove button is clicked
    $(wrapper).on('click', '.remove_button', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        x--; //Decrement field counter
    });

    $(wrapperLangkah).on('click', '.remove_button_langkah', function(e){
        e.preventDefault();
        $(this).parent('div').remove(); //Remove field html
        y--; //Decrement field counter
    });
});
</script>