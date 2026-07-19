<?php
/**
 * Intro block shown above the Projects / Sprints / Tasks listings, explaining what the
 * thing on this page is. Expects $intro:
 *   key    - short id, used for the collapse target and the remembered open/closed state
 *   title  - heading
 *   icon   - bootstrap-icons class for the heading
 *   lead   - one-paragraph explanation
 *   points - array of short bullets
 *   image  - filename in assets/images, shown to the left of the text
 *   active - which node of the Project > Sprint > Task diagram to highlight
 */
$introKey    = $intro['key'];
$introActive = isset($intro['active']) ? $intro['active'] : $introKey;
$nodes = [
    'project' => ['label' => 'PROJECT', 'x' => 10],
    'sprint'  => ['label' => 'SPRINT',  'x' => 140],
    'task'    => ['label' => 'TASK',    'x' => 270],
];
?>
<div class="row justify-content-center mb-4 mt-3">
    <div class="col-lg-10 col-md-11">
        <div class="card shadow-sm portal-intro-card">
            <div class="card-header d-flex align-items-center justify-content-between text-white" style="background-color: var(--customersPortalBackground);">
                <h5 class="mb-0"><i class="bi <?php echo $intro['icon'];?> me-2"></i><?php echo $intro['title'];?></h5>
                <button class="btn btn-sm btn-link text-white text-decoration-none p-0 portal-intro-toggle" type="button"
                        data-bs-toggle="collapse" data-bs-target="#intro-<?php echo $introKey;?>"
                        aria-expanded="true" aria-controls="intro-<?php echo $introKey;?>"
                        data-intro-key="<?php echo $introKey;?>">
                    <i class="bi bi-chevron-up"></i><span class="visually-hidden">Show or hide this introduction</span>
                </button>
            </div>
            <div class="collapse show" id="intro-<?php echo $introKey;?>">
                <div class="card-body">
                    <div class="row align-items-center g-3">
                        <?php if(!empty($intro['image'])):?>
                        <div class="col-lg-3 col-md-4">
                            <!-- Decorative only: the text beside it carries the meaning -->
                            <img src="<?php echo base_url('assets/images/'.$intro['image']);?>" alt=""
                                 class="img-fluid rounded w-100"
                                 style="height:170px; object-fit:cover; filter:grayscale(100%);" loading="lazy">
                        </div>
                        <?php endif;?>
                        <div class="col-lg-5 col-md-8">
                            <p class="text-muted mb-3"><?php echo $intro['lead'];?></p>
                            <ul class="mb-0 ps-3">
                                <?php foreach($intro['points'] as $point):?>
                                    <li class="mb-1 small"><?php echo $point;?></li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                        <div class="col-lg-4 col-md-12 text-center">
                            <!-- Project > Sprint > Task, with this page's level highlighted -->
                            <svg viewBox="0 0 380 90" role="img" aria-label="A project contains sprints, and a sprint contains tasks"
                                 style="width:100%; max-width:340px; height:auto;">
                                <?php foreach($nodes as $name => $node):
                                    $isActive = ($name === $introActive);
                                    $fill   = $isActive ? 'var(--customersPortalBackground)' : '#f1f3f5';
                                    $stroke = $isActive ? 'var(--customersPortalBackground)' : '#ced4da';
                                    $text   = $isActive ? '#ffffff' : '#6c757d';
                                ?>
                                    <rect x="<?php echo $node['x'];?>" y="25" width="100" height="40" rx="8"
                                          fill="<?php echo $fill;?>" stroke="<?php echo $stroke;?>" stroke-width="1.5"></rect>
                                    <text x="<?php echo $node['x'] + 50;?>" y="50" text-anchor="middle" dominant-baseline="middle"
                                          font-family="system-ui, sans-serif" font-size="13" font-weight="600"
                                          fill="<?php echo $text;?>"><?php echo $node['label'];?></text>
                                <?php endforeach;?>
                                <!-- connectors -->
                                <path d="M115 45 L133 45" stroke="#ced4da" stroke-width="1.5" fill="none"></path>
                                <path d="M133 45 l-5 -3.5 v7 z" fill="#ced4da"></path>
                                <path d="M245 45 L263 45" stroke="#ced4da" stroke-width="1.5" fill="none"></path>
                                <path d="M263 45 l-5 -3.5 v7 z" fill="#ced4da"></path>
                                <text x="124" y="18" text-anchor="middle" font-family="system-ui, sans-serif" font-size="10" fill="#adb5bd">contains</text>
                                <text x="254" y="18" text-anchor="middle" font-family="system-ui, sans-serif" font-size="10" fill="#adb5bd">contains</text>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Remember whether the reader collapsed this intro, so it stays out of the way on return visits.
(function(){
    var key = 'portalIntro:<?php echo $introKey;?>';
    var pane = document.getElementById('intro-<?php echo $introKey;?>');
    var icon = document.querySelector('.portal-intro-toggle[data-intro-key="<?php echo $introKey;?>"] i');
    if(!pane || !icon) return;

    if(localStorage.getItem(key) === 'closed'){
        pane.classList.remove('show');
        icon.classList.replace('bi-chevron-up','bi-chevron-down');
    }
    pane.addEventListener('shown.bs.collapse', function(){
        localStorage.setItem(key,'open');
        icon.classList.replace('bi-chevron-down','bi-chevron-up');
    });
    pane.addEventListener('hidden.bs.collapse', function(){
        localStorage.setItem(key,'closed');
        icon.classList.replace('bi-chevron-up','bi-chevron-down');
    });
})();
</script>
