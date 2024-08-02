<div style="font-family: 'Arial', sans-serif; font-size: 12px; margin: 10px;">
    <x-application-logo style="font-size: 30px; font-weight: bold; color: #c4c4c4;" />

    <!-- Lista de usuarios -->
    <h1 style="font-size: 16px; text-align: center; font-weight: bold; margin-bottom: 10px;">Recibo de Ventas</h1>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; text-align: center">
        <thead>
            <tr style="background-color: #c4c4c4;">
                <th style="padding: 3px;">No. Factura</th>
                <th style="padding: 3px;">Total</th>
                <th style="padding: 3px;">Fecha</th>
                <th style="padding: 3px;">Cliente</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ventas as $venta)
                <tr wire:key='{{ $venta->id }}'>
                    <td style="padding: 3px; border-bottom: .1px dotted #c4c4c4;">{{ $venta->numero_factura }}</td>
                    <td style="padding: 3px; border-bottom: .1px dotted #c4c4c4;">$
                        {{ number_format($venta->total, 0, ',', '.') }}</td>
                    <td style=" padding: 3px; border-bottom: .1px dotted #c4c4c4;">{{ $venta->created_at }}</td>
                    <td style=" padding: 3px; border-bottom: .1px dotted #c4c4c4; text-transform: capitalize">
                        {{ $venta->cliente->nombre ?? 'Super Usuario' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>


