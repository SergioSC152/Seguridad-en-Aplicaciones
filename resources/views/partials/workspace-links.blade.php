@foreach(['clients.manage'=>['admin.clients.index','Clientes'], 'leads.manage'=>['admin.leads.index','Prospectos'], 'sales-pipeline.manage'=>['admin.sales-pipeline.index','Pipeline'], 'products.manage'=>['admin.products.index','Productos y servicios'], 'quotes.manage'=>['admin.quotes.index','Cotizaciones y contratos'], 'sales.manage'=>['admin.sales.index','Ventas y despachos'], 'activities.manage'=>['admin.activities.index','Actividades y agenda'], 'auctions.manage'=>['admin.auctions.index','Remates'], 'content.manage'=>['admin.portal-blocks.index','CMS del portal'], 'audit.view'=>['admin.security.index','Auditoría']] as $permission=>$link)
@continue(($extendedOnly ?? false) && in_array($permission,['clients.manage','leads.manage','sales-pipeline.manage']))
@can('permission',$permission)<a href="{{ route($link[0]) }}">{{ $link[1] }}</a>@endcan
@endforeach
@if(!($extendedOnly ?? false))<a href="{{ route('admin.livestock-batches.index') }}">Lotes</a><a href="{{ route('admin.livestock-categories.index') }}">Categorías</a>@endif
@can('permission','leads.manage')<a href="{{ route('admin.whatsapp.index') }}">Bandeja WhatsApp</a>@endcan
<a href="{{ route('admin.security.account') }}">Seguridad de mi cuenta</a><a href="{{ route('catalog.index') }}">Catálogo público</a>
@if(auth()->user()->hasPermissionTo('sales.manage') || auth()->user()->hasPermissionTo('sales-pipeline.manage') || auth()->user()->hasPermissionTo('clients.manage'))<a href="{{ route('admin.insights.index') }}">Indicadores y alertas VIP</a>@endif
