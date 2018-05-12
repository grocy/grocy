<ul class="nav navbar-nav navbar-right">
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ HTTP_USER }} <span class="caret"></span></a>
		<ul class="dropdown-menu">
			<li>
				<a class="discrete-link logout-button" href="{{ $U('/logout') }}"><i class="fa fa-sign-out fa-fw"></i>&nbsp;{{ $L('Logout') }}</a>
			</li>
			<li role="separator" class="divider"></li>
			<li>
				<a class="discrete-link" href="{{ $U('/manageapikeys') }}"><i class="fa fa-handshake-o fa-fw"></i>&nbsp;{{ $L('Manage API keys') }}</a>
			</li>
			<li>
				<a class="discrete-link" target="_blank" href="{{ $U('/api') }}"><i class="fa fa-book"></i>&nbsp;{{ $L('REST API & data model documentation') }}</a>
			</li>
		</ul>
	</li>
</ul>
