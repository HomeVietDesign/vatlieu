<?php
if(is_tax('design_cat')):

$queried = get_queried_object();
$current_url = preg_replace('/page\/\d+/', '', fw_current_url());

$pro = isset($_GET['pro']) ? absint($_GET['pro']) : 0;
$int = isset($_GET['int']) ? absint($_GET['int']) : 0;
$ext = isset($_GET['ext']) ? absint($_GET['ext']) : 0;

$occ = isset($_GET['occ']) ? absint($_GET['occ']) : 0;

$tax_of_cat = fw_get_db_term_option($queried->term_id, 'design_cat', 'tax_active', []);
?>
<nav class="navbar navbar-vertical navbar-expand-lg">
<div class="collapse navbar-collapse" id="navbarVerticalCollapse">
<!-- scrollbar removed-->
	<?php

	?>
	<div class="navbar-vertical-content">
		<ul class="navbar-nav flex-column">
		<?php
		$projects = get_terms([
			'taxonomy' => 'project',
			'hide_empty' => false,
			'parent' => 0,
			'orderby' => 'name',
			'order' => 'ASC'
		]);

		if($projects) {
		?>
		<li class="nav-item">
			<div class="navbar-vertical-label d-flex justify-content-between align-items-center">
				<span>Dự án</span>
			</div>
			<hr class="navbar-vertical-line">
			<div class="nav-item-wrapper nav-search-term px-4 mb-2">
				<input type="text" id="search-project" class="form-control form-control-sm" placeholder="Tìm...">
			</div>
			<div id="project-list-search" class="hidden">
			</div>
			<div id="project-list">
			<?php

			foreach ($projects as $key => $value) {
				$url = add_query_arg('pro', $value->term_id, $current_url);
				$url = remove_query_arg('int', $url);
				$url = remove_query_arg('ext', $url);

				$class = ($pro==$value->term_id)?'active':'';

				?>
				<div class="nav-item-wrapper">
					<a class="nav-link text-uppercase dropdown-indicator label-1 <?=$class?>" href="<?=esc_url($url)?>" role="button" title="<?=esc_attr($value->description)?>">
						<div class="d-flex align-items-center">
							<span class="nav-link-text-wrapper">
								<span class="nav-link-icon"></span>
								<span class="nav-link-text"><?=esc_html($value->name)?></span>
							</span>
						</div>
					</a>
					<?php
					if($pro && $value->term_id==$pro && !empty($tax_of_cat)) {
					?>
					<div class="parent-wrapper">
						<ul class="nav">
							<?php
							foreach ($tax_of_cat as $value) {
								$subs = fw_get_db_term_option($pro, 'project', $value, []);
								if(!empty($subs)) {
									foreach ($subs as $sub) {
										$url = $current_url;
										if($value=='design_interior') {
											$url = remove_query_arg( 'ext', $current_url );
											$url = add_query_arg( 'int', $sub, $url );
										}elseif($value=='design_exterior') {
											$url = remove_query_arg( 'int', $current_url );
											$url = add_query_arg( 'ext', $sub, $url );
										}
										?>
										<li class="nav-item">
											<a class="nav-link <?php echo (($value=='design_interior' && $sub==$int) || ($value=='design_exterior' && $sub==$ext)) ? 'active':''; ?>" href="<?=esc_url($url)?>">
												<div class="d-flex align-items-center"><span class="nav-link-text"><?=esc_html(get_term_field( 'name', $sub, $value ))?></span></div>
											</a>
										</li>
										<?php
									}
								}
							}
							?>
						</ul>
					</div>
					<?php } ?>
				</div>
				<?php
			}
			?>
			</div>
		</li>
		<?php
		}
		?>
		</ul>
	</div>
</div>
</nav>
<?php
endif; //end is tax design_cat