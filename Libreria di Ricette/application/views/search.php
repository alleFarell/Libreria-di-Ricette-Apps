<!-- slider_area_start -->
<!-- Slider digunakan untuk menampilkan iklan atau promo yang tersedia -->
<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner"style="height: 400px">
      <div class="carousel-item active">
        <img src="<?= base_url("assets/img/1.jpg") ?>" class="d-block w-100">
      </div>
      <div class="carousel-item">
        <img src="<?= base_url("assets/img/2.jpg") ?>" class="d-block w-100">
      </div>
      <div class="carousel-item">
        <img src="<?= base_url("assets/img/3.jpg") ?>" class="d-block w-100">
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
</div>
<!-- slider_area_end -->

<!-- recepie_area_start  -->
<!-- Menampilkan resep-resep yang tersedia, dapat dilakukan sort berdasarkan triteria tertentu, selain itu dapat juga dilakukan pencarian resep berdasarkan nama hidangan -->
<div style="margin-bottom: 200px">
  <div class="recepie_area">
  <div class="container">
    <div class="row" style="margin-bottom: 20px">
      <div class="col-lg-6">
        <h2 >Recipes of the Day</h2>
      </div>
      <div class="col-lg-6">
        <?php echo form_open('RecipeController/search') ?>
        <div class="row">
          <div class="input-group-icon mt-10 col-lg-9" style="outline-style: solid; outline-color: #ff5e13;">
            <div class="icon" style="margin-top: 10px"><i class="fa fa-search" aria-hidden="true"></i></div>
            <input type="text" name="keyword" placeholder="Search" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Search'" required="" class="single-input">
          </div>
          <div class="col-lg-9" style="width: 15%;">
            <input type="submit" name="search_submit" value="Cari" class="genric-btn primary circle" style="border-bottom-left-radius: 25px; border-top-right-radius: 25px; border-bottom-right-radius: 5px; border-top-left-radius: 5px">
          </div>
        </div>
        <?php echo form_close() ?>
      </div>
      
    </div>
    <div class="row">
      <!-- content -->
      <?php if (empty($recipe)) { ?>
        <img class="card-img-top" style="height: 350px; width: 850px; display: block; margin-left: auto; margin-right: auto;" src="<?= base_url('assets/img/notFound.png')?>" alt="Card image cap">
      <?php } else {?>
      <?php foreach (array_reverse($recipe) as $index =>$r) { ?>
        <div class="col-xl-4 col-lg-4 col-md-6">
        <img class="card-img-top" style="height: 250px;" src="<?= base_url('assets/img/').$recipe[$index]['resepPic'] ?>" alt="Card image cap">
        <div class="card-body">
          <h3 class="card-title"><?= $recipe[$index]['judul'] ?></h3>
          <?php
            $empty_star = 5 - $recipe[$index]['rating'];
            for ($i = 0; $i < $recipe[$index]['rating']; $i++) {
          ?>
              <span class="fa fa-star checked"></span>
          <?php
            }

            for ($i = 0; $i < $empty_star; $i++) {
          ?>
              <span class="fa fa-star"></span>
          <?php } ?>
          <p><?= $recipe[$index]['rating'] ?>/5</p>
          <p>By <?= $member[$index]['username'] ?></p>
          <a href="<?= base_url('index.php/RecipeController/view_recipe/').$recipe[$index]['idResep'] ?>" class="genric-btn primary circle">View Full Recipe</a>
        </div>
      </div>
      <?php }} ?>
    </div>
  </div>
</div>
<div class="col">
</div>
</div> 

<!-- recepie_area_end  -->
