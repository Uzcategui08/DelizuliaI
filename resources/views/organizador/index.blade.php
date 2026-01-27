@extends('adminlte::page')

@php
	use Illuminate\Support\Str;

	$pendingTodos = $todos->where('is_completed', false);
	$completedTodos = $todos->where('is_completed', true);
	$overdueTodos = $pendingTodos->filter(fn ($todo) => $todo->due_at && $todo->due_at->isPast());
	$pendingPaymentsTotal = $pendingPayments->sum('amount');
	$completedPaymentsTotal = $completedPayments->sum('amount');
	$upcomingPaymentsToday = $pendingPayments->filter(fn ($payment) => $payment->scheduled_for && $payment->scheduled_for->isToday())->count();
@endphp

@section('title', 'Organizador')

@section('content_header')
<h1>Organizador</h1>
@stop

@section('content')
	<div class="container-fluid">
		@if(session('success'))
			<div class="alert alert-success">{{ session('success') }}</div>
		@endif

		<div class="row">
			<div class="col-sm-6 col-lg-3">
				<div class="small-box bg-primary">
					<div class="inner">
						<h3><span data-stat="pending-todos">{{ $pendingTodos->count() }}</span></h3>
						<p>Tareas pendientes</p>
					</div>
					<div class="icon">
						<i class="fas fa-clipboard-list"></i>
					</div>
					<a href="{{ route('todos.index') }}" class="small-box-footer">
						Ver lista <i class="fas fa-arrow-circle-right"></i>
					</a>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="small-box bg-danger">
					<div class="inner">
						<h3><span data-stat="overdue-todos">{{ $overdueTodos->count() }}</span></h3>
						<p>Tareas vencidas</p>
					</div>
					<div class="icon">
						<i class="fas fa-exclamation-triangle"></i>
					</div>
					<a href="{{ route('todos.index') }}" class="small-box-footer">
						Revisar pendientes <i class="fas fa-arrow-circle-right"></i>
					</a>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="small-box bg-warning">
					<div class="inner">
						<h3>Bs. <span data-stat="pending-payments-total">{{ number_format($pendingPaymentsTotal, 2, ',', '.') }}</span></h3>
						<p>Pagos pendientes (<span data-stat="pending-payments-count">{{ $pendingPayments->count() }}</span>)</p>
					</div>
					<div class="icon">
						<i class="fas fa-hand-holding-usd"></i>
					</div>
					<a href="{{ route('payments.index') }}" class="small-box-footer">
						Gestionar pagos <i class="fas fa-arrow-circle-right"></i>
					</a>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="small-box bg-success">
					<div class="inner">
						<h3>Bs. <span data-stat="completed-payments-total">{{ number_format($completedPaymentsTotal, 2, ',', '.') }}</span></h3>
						<p>Pagos realizados (<span data-stat="completed-payments-count">{{ $completedPayments->count() }}</span>)</p>
					</div>
					<div class="icon">
						<i class="fas fa-receipt"></i>
					</div>
					<a href="{{ route('payments.index') }}" class="small-box-footer">
						Ver historial <i class="fas fa-arrow-circle-right"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-6">
				<div class="card card-outline card-primary mb-4">
					<div class="card-header border-0">
						<div class="d-flex flex-wrap align-items-center justify-content-between">
							<div>
								<h3 class="card-title mb-0">
									<i class="fas fa-clipboard-check mr-2"></i>Tareas y recordatorios
								</h3>
								<span class="text-muted small">Organiza tus pendientes diarios</span>
							</div>
							<div class="btn-group btn-group-sm">
								<a href="{{ route('todos.index') }}" class="btn btn-outline-secondary">
									Ver todo
								</a>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div id="todoQuickAlert" class="alert alert-danger d-none"></div>

						<ul class="nav nav-pills" id="todoTabs" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="todos-pendientes-tab" data-toggle="tab" href="#todos-pendientes" role="tab" aria-controls="todos-pendientes" aria-selected="true">
									Pendientes (<span data-stat="pending-todos-label">{{ $pendingTodos->count() }}</span>)
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="todos-completados-tab" data-toggle="tab" href="#todos-completados" role="tab" aria-controls="todos-completados" aria-selected="false">
									Completadas (<span data-stat="completed-todos-label">{{ $completedTodos->count() }}</span>)
								</a>
							</li>
						</ul>
						<div class="tab-content pt-3" id="todoTabsContent">
							<div class="tab-pane fade show active" id="todos-pendientes" role="tabpanel" aria-labelledby="todos-pendientes-tab">
								<div class="list-group list-group-flush" id="pendingTodosList">
									@forelse($pendingTodos as $todo)
										@include('todos.partials.item', ['todo' => $todo, 'isCompleted' => false])
									@empty
										<div class="list-group-item text-center text-muted py-5" data-empty-state="pending">
											No tienes tareas pendientes.
										</div>
									@endforelse
								</div>
							</div>
							<div class="tab-pane fade" id="todos-completados" role="tabpanel" aria-labelledby="todos-completados-tab">
								<div class="list-group list-group-flush" id="completedTodosList">
									@forelse($completedTodos as $todo)
										@include('todos.partials.item', ['todo' => $todo, 'isCompleted' => true])
									@empty
										<div class="list-group-item text-center text-muted py-5" data-empty-state="completed">
											Aún no marcas tareas como completadas.
										</div>
									@endforelse
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-lg-6">
				<div class="card card-outline card-success mb-4">
					<div class="card-header border-0">
						<div class="d-flex flex-wrap align-items-center justify-content-between">
							<div>
								<h3 class="card-title mb-0">
									<i class="fas fa-hand-holding-usd mr-2"></i>Pagos y obligaciones
								</h3>
								<span class="text-muted small">
									{{ $upcomingPaymentsToday ? $upcomingPaymentsToday . ' pagos para hoy' : 'Mantén tus compromisos al día' }}
								</span>
							</div>
							<div class="btn-group btn-group-sm">
								<a href="{{ route('payments.create') }}" class="btn btn-success">
									<i class="fas fa-plus mr-1"></i> Registrar pago
								</a>
								<a href="{{ route('payees.index') }}" class="btn btn-outline-secondary">
									Destinatarios
								</a>
							</div>
						</div>
					</div>
					<div class="card-body">
						<ul class="nav nav-pills" id="paymentTabs" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="payments-pendientes-tab" data-toggle="tab" href="#payments-pendientes" role="tab" aria-controls="payments-pendientes" aria-selected="true">
									Pendientes (<span data-stat="pending-payments-count-label">{{ $pendingPayments->count() }}</span>)
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="payments-realizados-tab" data-toggle="tab" href="#payments-realizados" role="tab" aria-controls="payments-realizados" aria-selected="false">
									Realizados (<span data-stat="completed-payments-count-label">{{ $completedPayments->count() }}</span>)
								</a>
							</li>
						</ul>
						<div class="tab-content pt-3" id="paymentTabsContent">
							<div class="tab-pane fade show active" id="payments-pendientes" role="tabpanel" aria-labelledby="payments-pendientes-tab">
								<div class="list-group list-group-flush" id="pendingPaymentsList">
									@forelse($pendingPayments as $payment)
										@include('payments.partials.item', ['payment' => $payment, 'isCompleted' => false])
									@empty
										<div class="list-group-item text-center text-muted py-5" data-empty-state="pending-payments">
											No hay pagos pendientes. ¡Buen trabajo!
										</div>
									@endforelse
								</div>
							</div>
							<div class="tab-pane fade" id="payments-realizados" role="tabpanel" aria-labelledby="payments-realizados-tab">
								<div class="list-group list-group-flush" id="completedPaymentsList">
									@forelse($completedPayments as $payment)
										@include('payments.partials.item', ['payment' => $payment, 'isCompleted' => true])
									@empty
										<div class="list-group-item text-center text-muted py-5" data-empty-state="completed-payments">
											Todavía no registras pagos completados.
										</div>
									@endforelse
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
	const quickForm = document.getElementById('todoQuickForm');
	const alertBox = document.getElementById('todoQuickAlert');
	const pendingList = document.getElementById('pendingTodosList');
	const completedList = document.getElementById('completedTodosList');
	const quickSubmit = quickForm ? quickForm.querySelector('button[type="submit"]') : null;
	const dynamicSwitch = document.getElementById('todoDynamicToggle');
	let dynamicEnabled = dynamicSwitch ? dynamicSwitch.checked : true;

	const EMPTY_CONFIG = {
		pending: {
			state: 'pending',
			message: 'No tienes tareas pendientes.',
		},
		completed: {
			state: 'completed',
			message: 'Aún no marcas tareas como completadas.',
		},
	};

	const hideAlert = () => {
		if (!alertBox) {
			return;
		}

		alertBox.classList.add('d-none');
		alertBox.textContent = '';
		alertBox.classList.remove('alert-success');
		alertBox.classList.add('alert-danger');
	};

	const showAlert = (message, type = 'danger') => {
		if (!alertBox) {
			return;
		}

		alertBox.textContent = message;
		alertBox.classList.remove('d-none');
		alertBox.classList.toggle('alert-danger', type === 'danger');
		alertBox.classList.toggle('alert-success', type === 'success');
	};

	const removeEmptyState = (container) => {
		if (!container) {
			return;
		}

		container.querySelectorAll('[data-empty-state]').forEach((node) => node.remove());
	};

	const ensureEmptyState = (container, key) => {
		const config = EMPTY_CONFIG[key];
		if (!container || !config) {
			return;
		}

		if (container.querySelector('[data-todo-id]')) {
			return;
		}

		if (container.querySelector('[data-empty-state]')) {
			return;
		}

		const placeholder = document.createElement('div');
		placeholder.className = 'list-group-item text-center text-muted py-5';
		placeholder.dataset.emptyState = config.state;
		placeholder.textContent = config.message;
		container.appendChild(placeholder);
	};

	const updateStats = (stats) => {
		if (!stats || typeof stats !== 'object') {
			return;
		}

		Object.entries(stats).forEach(([key, value]) => {
			document.querySelectorAll(`[data-stat="${key}"]`).forEach((element) => {
				element.textContent = value;
			});
		});
	};

	const setButtonState = (button, disabled) => {
		if (!button) {
			return;
		}

		button.disabled = disabled;
	};

	if (dynamicSwitch) {
		dynamicSwitch.addEventListener('change', () => {
			dynamicEnabled = dynamicSwitch.checked;
			if (!dynamicEnabled) {
				hideAlert();
			}
		});
	}

	if (quickForm) {
		quickForm.addEventListener('submit', async (event) => {
			if (!dynamicEnabled) {
				return;
			}

			event.preventDefault();
			hideAlert();
			setButtonState(quickSubmit, true);

			const formData = new FormData(quickForm);

			try {
				const response = await fetch(quickForm.action, {
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'X-Requested-With': 'XMLHttpRequest',
					},
					body: formData,
				});

				if (response.ok) {
					const payload = await response.json();
					if (payload?.status === 'ok') {
						removeEmptyState(pendingList);
						if (payload.html && pendingList) {
							pendingList.insertAdjacentHTML('afterbegin', payload.html);
						}
						if (payload.stats) {
							updateStats(payload.stats);
						}
						quickForm.reset();
						showAlert(payload.message ?? 'Tarea creada correctamente.', 'success');
					} else {
						showAlert(payload?.message ?? 'No se pudo crear la tarea.');
					}
				} else if (response.status === 422) {
					const errorData = await response.json();
					const messages = Object.values(errorData.errors ?? {}).flat();
					showAlert(messages.join('\n') || 'Hay campos que necesitan tu atención.');
				} else {
					showAlert('Ocurrió un error al guardar la tarea. Intenta nuevamente.');
				}
			} catch (error) {
				console.error(error);
				showAlert('No fue posible conectar con el servidor. Revisa tu conexión.');
			} finally {
				setButtonState(quickSubmit, false);
			}
		});
	}

	document.addEventListener('submit', async (event) => {
		const form = event.target;
		if (!(form instanceof HTMLFormElement)) {
			return;
		}

		if (form.dataset.behavior !== 'toggle-todo') {
			return;
		}

		if (!dynamicEnabled) {
			return;
		}

		event.preventDefault();
		hideAlert();

		const toggleButton = form.querySelector('button[type="submit"]');
		const listItem = form.closest('[data-todo-id]');
		const container = listItem ? listItem.parentElement : null;
		const originKey = container && container.id === 'completedTodosList' ? 'completed' : 'pending';
		setButtonState(toggleButton, true);

		try {
			const response = await fetch(form.action, {
				method: form.getAttribute('method') || 'POST',
				headers: {
					'Accept': 'application/json',
					'X-Requested-With': 'XMLHttpRequest',
				},
				body: new FormData(form),
			});

			if (response.ok) {
				const payload = await response.json();
				if (payload?.status === 'ok') {
					if (listItem) {
						listItem.remove();
					}

					if (container) {
						ensureEmptyState(container, originKey);
					}

					const targetList = payload.is_completed ? completedList : pendingList;
					const targetKey = payload.is_completed ? 'completed' : 'pending';

					if (targetList) {
						removeEmptyState(targetList);
						if (payload.html) {
							targetList.insertAdjacentHTML('afterbegin', payload.html);
						}
						ensureEmptyState(targetList, targetKey);
					}

					if (payload.stats) {
						updateStats(payload.stats);
					}

					showAlert(payload.message ?? 'Estado de la tarea actualizado.', 'success');
				} else {
					showAlert(payload?.message ?? 'No se pudo actualizar la tarea.');
				}
			} else if (response.status === 422) {
				const errorData = await response.json();
				const messages = Object.values(errorData.errors ?? {}).flat();
				showAlert(messages.join('\n') || 'Hay campos que necesitan tu atención.');
			} else {
				showAlert('Ocurrió un error al actualizar la tarea. Intenta nuevamente.');
			}
		} catch (error) {
			console.error(error);
			showAlert('No fue posible conectar con el servidor. Revisa tu conexión.');
		} finally {
			setButtonState(toggleButton, false);
		}
	});
});
</script>
@endpush
