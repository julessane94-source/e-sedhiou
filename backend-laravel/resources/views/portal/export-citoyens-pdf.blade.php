<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<style>
    @page { size: A4 landscape; margin: 14mm 10mm; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #18222c; }
    h1 { font-size: 15px; margin-bottom: 3px; }
    .sub { color: #53616d; font-size: 9px; margin-bottom: 14px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #0a6c74; color: #fff; padding: 5px 6px; text-align: left; white-space: nowrap; }
    td { padding: 4px 6px; border-bottom: 1px solid #e0d8cc; vertical-align: top; word-break: break-word; }
    tr:nth-child(even) td { background: #f9f5ee; }
    .badge-oui { color: #1d6f42; font-weight: 600; }
    .badge-non { color: #a13b2b; font-weight: 600; }
    .col-email { max-width: 120px; }
    .col-addr  { max-width: 130px; }
</style>
</head>
<body>
<h1>Liste des citoyens — {{ $typeLabel }}</h1>
<p class="sub">Exporté le {{ $date }} — {{ $citoyens->count() }} citoyen(s)</p>

<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Adresse</th>
            <th>Date naiss.</th>
            <th>Lieu naiss.</th>
            <th>N° registre</th>
            <th>N° citoyen</th>
            <th>Doss.</th>
            <th>Actif</th>
            <th>Inscrit le</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($citoyens as $c)
            <tr>
                <td>{{ $c->last_name ?? $c->name }}</td>
                <td>{{ $c->first_name ?? '' }}</td>
                <td class="col-email">{{ $c->email }}</td>
                <td>{{ $c->phone ?? '—' }}</td>
                <td class="col-addr">{{ $c->address ?? '—' }}</td>
                <td>{{ optional($c->birth_date)->format('d/m/Y') ?? '—' }}</td>
                <td>{{ $c->birth_place ?? '—' }}</td>
                <td>{{ $c->register_number ?? '—' }}</td>
                <td>{{ $c->citizen_number ?? '—' }}</td>
                <td>{{ $c->demandes_count }}</td>
                <td class="{{ $c->is_active ? 'badge-oui' : 'badge-non' }}">{{ $c->is_active ? 'Oui' : 'Non' }}</td>
                <td>{{ optional($c->created_at)->format('d/m/Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>
