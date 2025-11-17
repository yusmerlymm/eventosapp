<?php $__env->startSection('title', 'Crear evento'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-semibold">Crear evento</h1>
  <a href="/admin/dashboard" class="text-sm text-blue-600 hover:underline">← Volver al Dashboard</a>
</div>

<form id="event-form" class="grid gap-6 max-w-3xl">
  <div class="grid gap-2">
    <label class="text-sm font-medium">Título</label>
    <input name="nombre" type="text" class="w-full rounded-md border px-3 py-2" required />
  </div>

  <div class="grid gap-2">
    <label class="text-sm font-medium">Descripción</label>
    <textarea name="descripcion" class="w-full rounded-md border px-3 py-2" rows="4" required></textarea>
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
    <div class="grid gap-2">
      <label class="text-sm font-medium">Inicio</label>
      <input name="fecha_inicio" type="datetime-local" class="w-full rounded-md border px-3 py-2" required />
    </div>
    <div class="grid gap-2">
      <label class="text-sm font-medium">Fin</label>
      <input name="fecha_fin" type="datetime-local" class="w-full rounded-md border px-3 py-2" required />
    </div>
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
    <div class="grid gap-2">
      <label class="text-sm font-medium">Audiencia</label>
      <select name="audiencia" class="w-full rounded-md border px-3 py-2">
        <option value="general">General</option>
        <option value="estudiantes">Estudiantes</option>
        <option value="profesores">Profesores</option>
        <option value="jubilados">Jubilados</option>
      </select>
    </div>
    <div class="grid gap-2">
      <label class="text-sm font-medium">Capacidad</label>
      <input name="capacidad_max" type="number" min="1" class="w-full rounded-md border px-3 py-2" required />
    </div>
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
    <div class="grid gap-2">
      <label class="text-sm font-medium">Venta - inicio</label>
      <input name="venta_inicio" type="datetime-local" class="w-full rounded-md border px-3 py-2" />
    </div>
    <div class="grid gap-2">
      <label class="text-sm font-medium">Venta - fin</label>
      <input name="venta_fin" type="datetime-local" class="w-full rounded-md border px-3 py-2" />
    </div>
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
    <div class="grid gap-2">
      <label class="text-sm font-medium">Categoría</label>
      <select name="categoryId" id="categoryId" class="w-full rounded-md border px-3 py-2" required></select>
    </div>
    <div class="grid gap-2">
      <label class="text-sm font-medium">Tipo</label>
      <select name="typeId" id="typeId" class="w-full rounded-md border px-3 py-2" required></select>
    </div>
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
    <div class="grid gap-2">
      <label class="text-sm font-medium">Lugar</label>
      <select name="venues_id" id="venues_id" class="w-full rounded-md border px-3 py-2" required></select>
    </div>
    <div class="grid gap-2">
      <label class="text-sm font-medium">Estado</label>
      <select name="status" id="status" class="w-full rounded-md border px-3 py-2" required></select>
    </div>
  </div>

  <div class="grid gap-2">
    <label class="text-sm font-medium">Imágenes</label>
    <input name="files[]" type="file" accept="image/*" multiple />
  </div>

  <div class="grid gap-2">
    <div class="flex items-center justify-between">
      <label class="text-sm font-medium">Tipos de entrada</label>
      <button type="button" id="add-ticket" class="text-sm rounded-md border px-2 py-1 hover:bg-gray-50">Añadir</button>
    </div>
    <div id="tickets" class="grid gap-2"></div>
  </div>

  <div id="form-errors" class="hidden rounded-md border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm"></div>

  <div class="flex gap-3">
    <button type="submit" class="rounded-md bg-gray-900 text-white px-4 py-2 hover:bg-black">Guardar</button>
    <a href="/events" class="rounded-md border px-4 py-2 hover:bg-gray-50">Cancelar</a>
  </div>
</form>

<template id="ticket-row">
  <div class="grid grid-cols-5 gap-2 items-center">
    <input placeholder="Nombre" class="col-span-3 rounded-md border px-3 py-2" />
    <input placeholder="Precio" type="number" step="0.01" min="0" class="col-span-1 rounded-md border px-3 py-2" />
    <button type="button" class="col-span-1 rounded-md border px-3 py-2 hover:bg-gray-50" data-action="remove">Quitar</button>
  </div>
</template>

<script src="/js/create-event-v2.js?v=<?php echo e(time()); ?>"></script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('events::layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\api-gestion-eventos\app-modules\events\resources\views/create-new.blade.php ENDPATH**/ ?>