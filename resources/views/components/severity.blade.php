@props(['finding'])

<span class="badge sev-{{ $finding->severity }}">{{ $finding->severityLabel() }}</span>
