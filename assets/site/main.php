<style>
    .main-content{
    color:blueviolet
    }
    .loader {
    width: 60px;
    display: flex;
    align-items: flex-start;
    aspect-ratio: 1;
    }
    .loader:before,
    .loader:after {
    content: "";
    flex: 1;
    aspect-ratio: 1;
    --g: conic-gradient(from -90deg at 10px 10px,blueviolet 90deg,#0000 0);
    background: var(--g), var(--g), var(--g);
    filter: drop-shadow(30px 30px 0 blueviolet);
    animation: l20 1s infinite;
    }
    .loader:after {
    transform: scaleX(-1);
    }
    @keyframes l20 {
    0%   {background-position:0     0, 10px 10px, 20px 20px}
    33%  {background-position:10px  10px}
    66%  {background-position:0    20px,10px 10px,20px 0   }
    100% {background-position:0     0, 10px 10px, 20px 20px}
    }
    .loader-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 60vh;
    }
</style>

<div class = "main-content">
    <h3>Stan: Obecnie w trakcie budowy.</h3>
</div>

<div class="loader-wrapper">
    <div class="loader"></div>
</div>