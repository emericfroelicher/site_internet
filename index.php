<?php
/* ---------- SESSION & BDD ---------- */
session_start();
require_once 'config.php';

$user = null;
if (!empty($_SESSION['user_id'])) {
  $stmt = $pdo->prepare('SELECT username FROM users WHERE id = ? LIMIT 1');
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetchColumn() ?: null;
}

/* ---------- DONNÉES OFFRES / AVIS ---------- */
$offers = [
  'defis'     => ['roi'=>'+25%', 'mise'=>'50 €',  'nb'=>'30/mois', 'cout'=>'49 €/mois', 'price'=>'49 €'],
  'cote'      => ['roi'=>'+40%', 'mise'=>'20 €',  'nb'=>'10/mois', 'cout'=>'29 €/mois', 'price'=>'29 €'],
  'nba'       => ['roi'=>'+32%', 'mise'=>'30 €',  'nb'=>'25/mois', 'cout'=>'39 €/mois', 'price'=>'39 €'],
  'moneyline' => ['roi'=>'+18%', 'mise'=>'100 €', 'nb'=>'15/mois', 'cout'=>'59 €/mois', 'price'=>'59 €'],
  'autres'    => ['roi'=>'+22%', 'mise'=>'40 €',  'nb'=>'20/mois', 'cout'=>'35 €/mois', 'price'=>'35 €']
];

$testimonials = [
  ['quote'=>'« J’ai triplé ma bankroll en six mois grâce aux défis »',                'author'=>'- Antoine B.'],
  ['quote'=>'« Les analyses NBA sont incroyablement précises : +35 % sur ma saison »','author'=>'- Laura M.'],
  ['quote'=>'« La côte boostée a payé mes vacances d’été ! »',                         'author'=>'- Marc L.'],
  ['quote'=>'« Service au top et community manager très réactif. Je recommande ! »',  'author'=>'- Sofia G.']
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trinidad Betting</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root{--bg-dark:#0D1117;--bg-card:#161B22;--text-light:#F2F2F2;--primary:#FFC845;--secondary:#1E8E3E;}
    *{margin:0;padding:0;box-sizing:border-box;font-family:'Montserrat',Arial,sans-serif;color:var(--text-light);}
    body{background:var(--bg-dark);}

    /* ---------- HEADER ---------- */
    header{display:flex;align-items:center;justify-content:space-between;padding:1rem 2rem;background:#121721;box-shadow:0 2px 4px rgba(0,0,0,.5);}
    header img{height:48px;}
    header h1{font-size:1.6rem;font-weight:600;}
    .btn{cursor:pointer;padding:.6rem 1.2rem;margin-left:.5rem;border:none;border-radius:6px;font-weight:600;background:var(--primary);color:#000;transition:transform .15s;}
    .btn:hover{transform:scale(1.05);}
    .username{background:#161B22;color:#FFC845;padding:.6rem 1.2rem;border-radius:6px;font-weight:600;margin-right:.5rem;}

    /* ---------- WELCOME ---------- */
    .welcome{background:#161B22;padding:1rem 2rem;border-bottom:2px solid var(--primary);}
    .welcome h2{font-size:1.3rem;font-weight:600;margin-bottom:.4rem;}
    .welcome p{font-size:.95rem;}

    /* ---------- HERO ---------- */
    .hero{display:flex;flex-wrap:wrap;gap:.5rem;padding:3rem 2rem;}
    .hero-left{flex:1 1 320px;max-width:560px;}
    .hero-left p{font-size:1.4rem;line-height:1.4;font-weight:600;max-width:34rem;margin-bottom:.5rem;}
    .testimonials{margin-top:2rem;display:flex;align-items:center;gap:1rem;}
    .arrow{background:transparent;border:none;font-size:2rem;cursor:pointer;color:var(--primary);transition:transform .15s;}
    .arrow:hover{transform:scale(1.15);}
    .testimonial{flex:1;background:var(--bg-card);border-left:4px solid var(--primary);padding:1rem 1.2rem;border-radius:8px;min-height:120px;}
    .quote{font-size:1rem;margin-bottom:.6rem;}.author{font-size:.9rem;font-weight:600;text-align:right;}

    .hero-right{flex:0 0 520px;max-width:520px;margin-left:auto;}
    .card{background:var(--bg-card);border-radius:12px;padding:2.5rem 2rem;box-shadow:0 4px 8px rgba(0,0,0,.6);}
    .card h2{font-size:1.4rem;margin-bottom:1rem;}
    .markets{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem;}
    .market-btn{background:#2D333B;border:1px solid #444C56;padding:.4rem .8rem;border-radius:6px;font-size:.85rem;cursor:pointer;transition:background .2s;}
    .market-btn.active,.market-btn:hover{background:var(--secondary);}
    .caracs{margin:.5rem 0 1.5rem;} .caracs h3{font-size:1rem;margin-bottom:.4rem;} .caracs ul{list-style:none;} .caracs li{font-size:.9rem;margin:.25rem 0;}
    .simulator{margin-bottom:1.5rem;} .simulator label{font-size:.9rem;display:block;margin-bottom:.3rem;}
    .simulator input[type=range]{width:100%;appearance:none;height:6px;background:#444C56;border-radius:3px;}
    .simulator input[type=range]::-webkit-slider-thumb{appearance:none;width:14px;height:14px;border-radius:50%;background:var(--primary);cursor:pointer;border:none;}
    .cart{display:flex;align-items:center;justify-content:space-between;} .cart .price{font-size:1.1rem;font-weight:600;}
    .cart-btn{background:var(--primary);color:#000;padding:.6rem 1.2rem;border:none;border-radius:6px;font-weight:600;}

    /* ---------- MOBILE (≤ 768 px) ---------- */
    @media (max-width:768px){
      /* HEADER */
      header{flex-wrap:wrap;}
      header img{height:44px;}
      header h1{order:3;width:100%;text-align:center;font-size:1.25rem;margin-top:.4rem;}
      header>div{order:2;margin-left:auto;}

      /* HERO : zéro espace */
      .hero{flex-direction:column;align-items:center;padding:1.2rem 1rem 0;gap:0;}
      .hero-left{max-width:100%;}
      .hero-left p{margin:0;text-align:center;}

      .hero-right{flex:1 1 100%;max-width:100%;margin:0;}      /* carte collée */
      .card{padding:1.2rem 1rem;}                               /* padding serré */

      .testimonials{width:100%;margin-top:1rem;}
      .testimonial{min-height:auto;}
    }
  </style>
</head>
<body>
  <!-- ---------- HEADER ---------- -->
  <header>
    <img src="img/logo.jpg" alt="Logo Trinidad"style="border-radius:50%; cursor:pointer"  >
    <h1>Trinidad Betting</h1>
    <div style="display:flex;align-items:center;">
      <?php if ($user): ?>
        <span class="username"><?= htmlspecialchars($user) ?></span>
        <a class="btn" href="logout.php">Déconnexion</a>
      <?php else: ?>
        <a class="btn" href="login.html">Se connecter</a>
        <a class="btn" href="signup.html">S’inscrire</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- ---------- WELCOME ---------- -->
  <?php if ($user): ?>
    <section class="welcome">
      <h2>Bienvenue <?= htmlspecialchars($user) ?> !</h2>
      <p>Vos abonnements en cours : Aucun</p>
    </section>
  <?php endif; ?>

  <!-- ---------- MAIN ---------- -->
  <main class="hero">
    <!-- Message + Avis -->
    <section class="hero-left">
      <p>Plusieurs centaines de milliers d’euros gagnés chaque année : transformez votre passion pour le sport en investissement rentable.</p>

      <div class="testimonials">
        <button class="arrow" id="prevT">&#8249;</button>
        <div class="testimonial" id="testimonialBox">
          <p class="quote"></p><p class="author"></p>
        </div>
        <button class="arrow" id="nextT">&#8250;</button>
      </div>
    </section>

    <!-- Carte Offres -->
    <section class="hero-right">
      <div class="card">
        <h2>Nos offres</h2>
        <div class="markets" id="marketsContainer"></div>

        <div class="caracs">
          <h3>Caractéristiques</h3>
          <ul>
            <li id="roi"></li><li id="mise"></li><li id="nb"></li><li id="cout"></li>
          </ul>
        </div>

        <div class="simulator">
          <h3>Simulateur de gain</h3>
          <label for="bankroll">Bankroll (€) : <span id="bankrollValue">1000</span></label>
          <input type="range" id="bankroll" min="100" max="20000" step="100" value="1000">
          <ul><li id="gainMois"></li><li id="gainAn"></li></ul>
        </div>

        <div class="cart">
          <span class="price" id="price"></span>
          <button class="cart-btn" id="addCart">Ajouter au panier 🛒</button>
        </div>
      </div>
    </section>
  </main>

  <!-- ---------- SCRIPTS ---------- -->
  <script>
  const offers       = <?= json_encode($offers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
  const testimonials = <?= json_encode($testimonials, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

  /* DOM refs */
  const hero         = document.querySelector('.hero');
  const heroLeft     = document.querySelector('.hero-left');
  const heroRight    = document.querySelector('.hero-right');
  const testimonialsDiv = document.querySelector('.testimonials');

  const bankroll   = document.getElementById('bankroll');
  const bankVal    = document.getElementById('bankrollValue');
  const gainMois   = document.getElementById('gainMois');
  const gainAn     = document.getElementById('gainAn');
  const markets    = document.getElementById('marketsContainer');

  /* Boutons marchés */
  Object.keys(offers).forEach((k,i)=>{
    const b=document.createElement('button');
    b.className='market-btn'+(i===0?' active':'');
    b.dataset.market=k;
    b.textContent=k.charAt(0).toUpperCase()+k.slice(1);
    markets.appendChild(b);
  });

  let currentROI=parseFloat(offers.defis.roi);
  const caracIds={roi:'roi',mise:'mise',nb:'nb',cout:'cout',price:'price'};

  function euro(x){return x.toLocaleString('fr-FR',{style:'currency',currency:'EUR',minimumFractionDigits:0});}
  function updateGains(){
    const bk=parseInt(bankroll.value,10);
    bankVal.textContent=bk;
    const m=bk*currentROI/100, a=m*12;
    gainMois.textContent=`Gain estimé par mois : ${euro(m)}`;
    gainAn.textContent  =`Gain estimé par an :  ${euro(a)}`;
  }
  function setOffer(k){
    const d=offers[k];
    document.getElementById(caracIds.roi).textContent =`ROI : ${d.roi}`;
    document.getElementById(caracIds.mise).textContent=`Mise moyenne : ${d.mise}`;
    document.getElementById(caracIds.nb).textContent  =`Nombre de paris moyen : ${d.nb}`;
    document.getElementById(caracIds.cout).textContent=`Coût abonnement : ${d.cout}`;
    document.getElementById(caracIds.price).textContent=d.price;
    currentROI=parseFloat(d.roi);
    updateGains();
  }

  bankroll.addEventListener('input',updateGains);
  markets.addEventListener('click',e=>{
    if(!e.target.classList.contains('market-btn'))return;
    document.querySelectorAll('.market-btn').forEach(b=>b.classList.remove('active'));
    e.target.classList.add('active');
    setOffer(e.target.dataset.market);
  });
  document.getElementById('addCart').addEventListener('click',()=>alert('Offre ajoutée au panier !'));

  /* Témoignages */
  const box=document.getElementById('testimonialBox');
  let idx=0;
  function showTest(i){
    box.querySelector('.quote' ).textContent=testimonials[i].quote;
    box.querySelector('.author').textContent=testimonials[i].author;
  }
  document.getElementById('prevT').onclick=()=>{idx=(idx-1+testimonials.length)%testimonials.length;showTest(idx);};
  document.getElementById('nextT').onclick=()=>{idx=(idx+1)%testimonials.length;showTest(idx);};

  /* Réorganisation mobile : offre puis avis */
  function adjustTestimonials(){
    if(window.innerWidth<=768){
      if(heroRight.nextSibling!==testimonialsDiv){
        hero.insertBefore(testimonialsDiv, heroRight.nextSibling);
      }
    }else{
      if(!heroLeft.contains(testimonialsDiv)){
        heroLeft.appendChild(testimonialsDiv);
      }
    }
  }
  window.addEventListener('resize',adjustTestimonials);

  /* Init */
  document.addEventListener('DOMContentLoaded',()=>{
    adjustTestimonials();
    setOffer('defis');
    showTest(0);
    updateGains();
  });
  </script>
</body>
</html>
