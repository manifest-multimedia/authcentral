<x-backend.dashboard>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Activity Log') }}</h3>
                    </div>
                    <div class="card-body">
                        @if($logs->isEmpty())
                            <div class="alert alert-info">
                                {{ __('No activity logs found.') }}
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Application') }}</th>
                                            <th>{{ __('Action') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('IP Address') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Date/Time') }}</th>
                                            <th>{{ __('Details') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($logs as $log)
                                            <tr>
                                                <td>
                                                    @if($log->app_url)
                                                        <a href="{{ $log->app_url }}" target="_blank" class="text-decoration-none">
                                                            {{ $log->app_name ?: 'Auth Central' }}
                                                            <i class="bi bi-box-arrow-up-right small ms-1"></i>
                                                        </a>
                                                    @else
                                                        {{ $log->app_name ?: 'Auth Central' }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $log->action == 'login' ? 'success' : ($log->action == 'logout' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($log->action) }}
                                                    </span>
                                                </td>
                                                <td>{{ $log->description }}</td>
                                                <td>{{ $log->ip_address }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $log->status == 'success' ? 'success' : ($log->status == 'error' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst($log->status ?: 'success') }}
                                                    </span>
                                                </td>
                                                <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#logDetails{{ $log->id }}">
                                                        <i class="bi bi-info-circle"></i>
                                                    </button>
                                                    
                                                    <!-- Log Details Modal -->
                                                    <div class="modal fade" id="logDetails{{ $log->id }}" tabindex="-1" aria-labelledby="logDetailsLabel{{ $log->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="logDetailsLabel{{ $log->id }}">{{ __('Activity Details') }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <h6>{{ __('Basic Information') }}</h6>
                                                                            <table class="table table-sm">
                                                                                <tr>
                                                                                    <th>{{ __('Application') }}</th>
                                                                                    <td>{{ $log->app_name ?: 'Auth Central' }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>{{ __('Action') }}</th>
                                                                                    <td>{{ ucfirst($log->action) }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>{{ __('Description') }}</th>
                                                                                    <td>{{ $log->description }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>{{ __('Date/Time') }}</th>
                                                                                    <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>{{ __('Status') }}</th>
                                                                                    <td>{{ ucfirst($log->status ?: 'success') }}</td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <h6>{{ __('Technical Details') }}</h6>
                                                                            <table class="table table-sm">
                                                                                <tr>
                                                                                    <th>{{ __('IP Address') }}</th>
                                                                                    <td>{{ $log->ip_address }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>{{ __('User Agent') }}</th>
                                                                                    <td><small>{{ $log->user_agent }}</small></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>{{ __('Method') }}</th>
                                                                                    <td>{{ $log->method }}</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <th>{{ __('Endpoint') }}</th>
                                                                                    <td>{{ $log->endpoint }}</td>
                                                                                </tr>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    @if($log->request_data)
                                                                    <div class="mt-3">
                                                                        <h6>{{ __('Request Data') }}</h6>
                                                                        <div class="bg-light p-2 rounded">
                                                                            <pre class="mb-0"><code>{{ json_encode($log->request_data, JSON_PRETTY_PRINT) }}</code></pre>
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                                    
                                                                    @if($log->entity_data)
                                                                    <div class="mt-3">
                                                                        <h6>{{ __('Entity Data') }}</h6>
                                                                        <div class="bg-light p-2 rounded">
                                                                            <pre class="mb-0"><code>{{ json_encode($log->entity_data, JSON_PRETTY_PRINT) }}</code></pre>
                                                                        </div>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $logs->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-backend.dashboard>