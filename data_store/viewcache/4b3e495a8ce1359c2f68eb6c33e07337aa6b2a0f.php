<?php $__env->startSection('title', $__t('Login')); ?>
<?php $__env->startSection('viewJsName', 'login'); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
	<div class="col-lg-6 offset-lg-3 col-xs-12">
		<h1 class="text-center"><?php echo $__env->yieldContent('title'); ?></h1>

		<form method="post" action="<?php echo e($U('/login')); ?>" id="login-form" novalidate>

			<div class="form-group">
				<label for="name"><?php echo e($__t('Username')); ?></label>
				<input type="text" class="form-control" required id="username" name="username">
			</div>

			<div class="form-group">
				<label for="name"><?php echo e($__t('Password')); ?></label>
				<input type="password" class="form-control" required id="password" name="password">
				<div id="login-error" class="form-text text-danger d-none"></div>
			</div>

			<div class="checkbox">
				<label for="stay_logged_in">
					<input type="checkbox" id="stay_logged_in" name="stay_logged_in"> <?php echo e($__t('Stay logged in permanently')); ?>

					<p class="form-text text-muted small my-0"><?php echo e($__t('When not set, you will get logged out at latest after 30 days')); ?></p>
				</label>
			</div>

			<button id="login-button" class="btn btn-success"><?php echo e($__t('OK')); ?></button>

		</form>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /www/views/login.blade.php ENDPATH**/ ?>