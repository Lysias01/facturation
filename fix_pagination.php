<?php
$file = 'resources/views/factures/index.blade.php';
$content = file_get_contents($file);

$search = "</tbody>\n    </table>\n</div>\n\n    @if(\$factures->hasPages())\n    <div class=\"card-footer bg-white\">\n        {{ \$factures->links() }}\n    </div>\n    @endif\n\n@endsection";

$replace = "</tbody>\n    </table>\n</div>\n    @if(\$factures->hasPages())\n    <div class=\"card-footer bg-white\">\n        <nav>\n            <ul class=\"pagination justify-content-center mb-0\" style=\"flex-wrap: wrap;\">\n                @foreach (\$factures->links()->elements as \$element)\n                    @if (is_array(\$element))\n                        @foreach (\$element as \$page => \$url)\n                            @if (\$page == \$factures->currentPage())\n                                <li class=\"page-item active\"><span class=\"page-link\" style=\"background-color: #0d6efd; border-color: #0d6efd;\">{\$page}</span></li>\n                            @else\n                                <li class=\"page-item\"><a class=\"page-link\" href=\"{{ \$url }}\" style=\"color: #0d6efd;\">{\$page}</a></li>\n                            @endif\n                        @endforeach\n                    @endif\n                @endforeach\n            </ul>\n        </nav>\n    </div>\n    @endif\n\n@endsection";

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done!";
