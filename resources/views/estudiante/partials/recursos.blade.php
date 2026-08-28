                            {{-- <div class="tab-pane fade" id="tab-resources" role="tabpanel"> --}}
                            @php
                                $documentosCurso = $curso->documentos ?? collect();
                            @endphp

                            @if($documentosCurso->isNotEmpty())
                                <h4 class="mb-3">Recursos del curso</h4>

                                <h5 class="mt-4">Documentos</h5>
                                <div class="list-group mb-4">
                                    @foreach($documentosCurso as $documento)
                                        <a href="{{ asset('storage/' . $documento->archivo) }}"
                                            target="_blank"
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-file-{{ $documento->tipo === 'pdf' ? 'pdf text-danger' : ($documento->tipo === 'zip' ? 'archive text-primary' : 'alt text-success') }}"></i>
                                                <span class="ml-2">{{ $documento->titulo }}</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            {{-- </div> --}}
