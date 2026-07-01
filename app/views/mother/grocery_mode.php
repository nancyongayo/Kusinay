<?php
$pageTitle = 'Grocery';
$activeNav = 'grocery';
require_once __DIR__ . '/../templates/mother_layout.php';
?>

<style>
.mode-card {
    position: relative;
    height: 180px;
    border-radius: 1rem;
    overflow: hidden;
    cursor: pointer;
    transition: all .3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
}

.mode-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
}

.mode-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.mode-card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(0,0,0,.6) 0%, rgba(0,0,0,.3) 100%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    padding: 2rem;
}

.mode-card-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: .5rem;
    text-shadow: 0 2px 8px rgba(0,0,0,.3);
}

.mode-card-subtitle {
    font-size: 1rem;
    opacity: .95;
    text-shadow: 0 1px 4px rgba(0,0,0,.3);
}

.category-card {
    position: relative;
    height: 140px;
    border-radius: .75rem;
    overflow: hidden;
    cursor: pointer;
    transition: all .2s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,.08);
}

.category-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(0,0,0,.12);
}

.category-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,.7) 0%, transparent 100%);
    padding: 1rem;
    color: white;
}

.category-name {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: .25rem;
    text-shadow: 0 1px 3px rgba(0,0,0,.5);
}

.category-desc {
    font-size: .75rem;
    opacity: .9;
    text-shadow: 0 1px 2px rgba(0,0,0,.5);
}
</style>

<div class="container-fluid py-4">
    <!-- Grocery Mode Selection -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h5 class="fw-bold mb-0" style="color:#C4722A">
                <i class="bi bi-cart3 me-2"></i>Grocery Mode
            </h5>
            <button class="btn btn-secondary btn-sm" onclick="window.location.href='index.php?action=myOrders'" style="background-color:#6c757d; border-color:#6c757d;">
                <i class="bi bi-receipt me-1"></i>View Orders
            </button>
        </div>
        <p class="text-muted mb-3">Choose where you will buy your ingredients</p>
        
        <div class="row g-3 mb-4">
            <!-- Supermarket -->
            <div class="col-md-6">
                <a href="index.php?action=supermarket" class="text-decoration-none">
                    <div class="mode-card">
                        <img src="https://images.unsplash.com/photo-1604719312566-8912e9227c6a?w=800&q=80" alt="Supermarket">
                        <div class="mode-card-overlay">
                            <div class="mode-card-title">Supermarket</div>
                            <div class="mode-card-subtitle">Browse aisles for easy navigation</div>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Wet Market -->
            <div class="col-md-6">
                <a href="index.php?action=wetMarket" class="text-decoration-none">
                    <div class="mode-card">
                        <img src="https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=800&q=80" alt="Wet Market">
                        <div class="mode-card-overlay">
                            <div class="mode-card-title">Wet Market</div>
                            <div class="mode-card-subtitle">Pili sa bag-o nga market vendor</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Quick Categories -->
    <div class="mb-4">
        <div class="card" style="border:1.5px solid rgba(196,114,42,.15);border-radius:1rem;background:white">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3" style="color:#C4722A">
                    <i class="bi bi-grid-3x3-gap-fill me-2"></i>Quick Categories
                </h5>
                
                <div class="row g-3">
                    <!-- Canned Goods -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Canned Goods" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://cdn.apartmenttherapy.info/image/upload/v1558649178/k/archive/df87781dc2802960a6d2a775db135a0cead0e657.jpg" alt="Canned Goods">
                                <div class="category-overlay">
                                    <div class="category-name">Canned Goods</div>
                                    <div class="category-desc">Shelf-stable items</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Rice -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Grains" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://media.philstar.com/photos/2023/04/05/rice_2023-04-05_21-27-39.jpg" alt="Rice">
                                <div class="category-overlay">
                                    <div class="category-name">Rice</div>
                                    <div class="category-desc">Big sacks & small packs</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Snacks -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Snacks" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://static.stacker.com/s3fs-public/styles/1280x720/s3/20EU1W.png?token=ras5Be2N" alt="Snacks">
                                <div class="category-overlay">
                                    <div class="category-name">Snacks</div>
                                    <div class="category-desc">Big sacks & small packs</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    
                    
                    <!-- Dairy -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Dairy" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://thumbs.dreamstime.com/b/dairy-products-supermarket-various-types-beaten-milk-97701820.jpg" alt="Dairy">
                                <div class="category-overlay">
                                    <div class="category-name">Dairy</div>
                                    <div class="category-desc">Milk, cheese & yogurt</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Eggs -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Protein" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://cdn.sanity.io/images/5dqbssss/production-v3/6b89f02327c3682ff97641e602658480e4ba2f0c-1600x1000.png?w=3840&q=75&fit=clip&auto=format" alt="Eggs">
                                <div class="category-overlay">
                                    <div class="category-name">Eggs</div>
                                    <div class="category-desc">Large, medium and small size</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Vegetables -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Vegetables" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://thumbs.dreamstime.com/b/local-fresh-vegetables-20559218.jpg" alt="Vegetables">
                                <div class="category-overlay">
                                    <div class="category-name">Vegetables</div>
                                    <div class="category-desc">Fresh produce</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Fruits -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Fruits" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://images.stockcake.com/public/9/0/0/9004f00d-64aa-479b-95d6-cd5745f43ad4_large/colorful-fruit-display-stockcake.jpg" alt="Fruits">
                                <div class="category-overlay">
                                    <div class="category-name">Fruits</div>
                                    <div class="category-desc">Seasonal fruits</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Meat -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Protein" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://adobochronicles.com/wp-content/uploads/2020/01/a2497590-60c0-42fd-a051-c472edb72df2.jpeg?w=584&h=259" alt="Meat">
                                <div class="category-overlay">
                                    <div class="category-name">Meat</div>
                                    <div class="category-desc">Pork, beef, chicken</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Fish -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Protein" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://4.bp.blogspot.com/GWolddf_JUTSL4wLSPkMFRGTbyWrFoFoGVAQBHgc9gSph1Mu2l5Skj4ml6a5BStRW82GbpXfTCBAsuOPUZRK_ryacGxzKVrAVt6cJDunfuYOsOvqHXIwQQcaUoE46fCBfMuG6kbHhHJqKr4Y9CuaQoU5kc_yq2cH-MabeLNBqt9nEmwOl75Opm-Cigow8x9yEnLN6FumP01LUY3lxQSlbvWn_p03qv62YO7P_g0f3J5qeMqLQ6O8Fw8m-7i1GhGK5vieBYcEelELFmt4m9Sc2jvurqsd_VJLF779BZptF1gjvPB2YKkxPGWLqt4kfQSRZVWKhXolpgQjr1EBGdSYvmhCq1KcVdr6O4J8r-B2d9tPsCDZT2a8942LhAnge1F03TmjII6da3DNjPbDtC50WqqdJ-zvzpbHUvzNlnTvnK89_jFU6ALBDKBDl_MT5HwAeKNln3Kl0U6zQupxOWhvS5OLCb0tOyi5noKKnp9NLlBa9g0icMwWz7bmJrkYZixQXZjv5mhpv-5yd4eB7S58LYIg_o0WpFu5jw37wDTIENnbkTz1B0Iqp432UwWYLJ6P-BfG=w640-h427-rw-no" alt="Fish">
                                <div class="category-overlay">
                                    <div class="category-name">Fish</div>
                                    <div class="category-desc">Fresh seafood</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- rinks -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Protein" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=400&q=80" alt="Poultry">
                                <div class="category-overlay">
                                    <div class="category-name">Poultry</div>
                                    <div class="category-desc">Chicken & duck</div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Poultry -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Protein" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://media.philstar.com/photos/2019/01/03/ssb-tax_2019-01-03_17-25-52.jpg" alt="Poultry">
                                <div class="category-overlay">
                                    <div class="category-name">Drinks</div>
                                    <div class="category-desc">Juices & Beverages</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Condiments -->
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <a href="index.php?action=supermarket&category=Condiments" class="text-decoration-none">
                            <div class="category-card">
                                <img src="https://images.unsplash.com/photo-1472476443507-c7a5948772fc?w=400&q=80" alt="Condiments">
                                <div class="category-overlay">
                                    <div class="category-name">Condiments</div>
                                    <div class="category-desc">Sauces & spices</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/mother_layout_end.php'; ?>
