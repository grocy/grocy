<ul class="nav navbar-nav ml-auto">
	<li class="dropdown nav-item">
		<a href="#" class="dropdown-toggle nav-link nav-link-navbar" data-toggle="dropdown">@if(AUTHENTICATED === true){{ HTTP_USER }}@endif <span class="caret"></span></a>
		<ul class="dropdown-menu dropdown-menu-right">
			<li class="dropdown-item">
				<a class="discrete-link logout-button" href="{{ $U('/logout') }}"><i class="fa fa-sign-out fa-fw"></i>&nbsp;{{ $L('Logout') }}</a>
			</li>
			<div class="dropdown-divider"></div>
			<li class="dropdown-item">
				<a class="discrete-link" href="{{ $U('/manageapikeys') }}"><i class="fa fa-handshake-o fa-fw"></i>&nbsp;{{ $L('Manage API keys') }}</a>
			</li>
			<li class="dropdown-item">
				<a class="discrete-link" target="_blank" href="{{ $U('/api') }}"><i class="fa fa-book"></i>&nbsp;{{ $L('REST API & data model documentation') }}</a>
			</li>
		</ul>
	</li>
</ul>
