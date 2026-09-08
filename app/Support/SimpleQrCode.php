<?php

namespace App\Support;

use InvalidArgumentException;

class SimpleQrCode
{
    private const MODE_8BIT_BYTE = 1 << 2;
    private const ERROR_CORRECTION_M = 0;
    private const G15 = 0b10100110111;
    private const G18 = 0b1111100100101;
    private const G15_MASK = 0b101010000010010;

    private const PATTERN_POSITION_TABLE = [
        [], [6, 18], [6, 22], [6, 26], [6, 30], [6, 34], [6, 22, 38], [6, 24, 42],
        [6, 26, 46], [6, 28, 50], [6, 30, 54], [6, 32, 58], [6, 34, 62], [6, 26, 46, 66],
        [6, 26, 48, 70], [6, 26, 50, 74], [6, 30, 54, 78], [6, 30, 56, 82],
        [6, 30, 58, 86], [6, 34, 62, 90], [6, 28, 50, 72, 94], [6, 26, 50, 74, 98],
        [6, 30, 54, 78, 102], [6, 28, 54, 80, 106], [6, 32, 58, 84, 110], [6, 30, 58, 86, 114],
        [6, 34, 62, 90, 118], [6, 26, 50, 74, 98, 122], [6, 30, 54, 78, 102, 126],
        [6, 26, 52, 78, 104, 130], [6, 30, 56, 82, 108, 134], [6, 34, 60, 86, 112, 138],
        [6, 30, 58, 86, 114, 142], [6, 34, 62, 90, 118, 146], [6, 30, 54, 78, 102, 126, 150],
        [6, 24, 50, 76, 102, 128, 154], [6, 28, 54, 80, 106, 132, 158], [6, 32, 58, 84, 110, 136, 162],
        [6, 26, 54, 82, 110, 138, 166], [6, 30, 58, 86, 114, 142, 170],
    ];

    private const RS_BLOCKS_M = [
        1 => [1,26,16], 2 => [1,44,28], 3 => [1,70,44], 4 => [2,50,32], 5 => [2,67,43],
        6 => [4,43,27], 7 => [4,49,31], 8 => [2,60,38,2,61,39], 9 => [3,58,36,2,59,37],
        10 => [4,69,43,1,70,44], 11 => [1,80,50,4,81,51], 12 => [6,58,36,2,59,37],
        13 => [8,59,37,1,60,38], 14 => [4,64,40,5,65,41], 15 => [5,65,41,5,66,42],
        16 => [7,73,45,3,74,46], 17 => [10,74,46,1,75,47], 18 => [9,69,43,4,70,44],
        19 => [3,70,44,11,71,45], 20 => [3,67,41,13,68,42], 21 => [17,68,42], 22 => [17,74,46],
        23 => [4,75,47,14,76,48], 24 => [6,73,45,14,74,46], 25 => [8,75,47,13,76,48],
        26 => [19,74,46,4,75,47], 27 => [22,73,45,3,74,46], 28 => [3,73,45,23,74,46],
        29 => [21,73,45,7,74,46], 30 => [19,75,47,10,76,48], 31 => [2,74,46,29,75,47],
        32 => [10,74,46,23,75,47], 33 => [14,74,46,21,75,47], 34 => [14,74,46,23,75,47],
        35 => [12,75,47,26,76,48], 36 => [6,75,47,34,76,48], 37 => [29,74,46,14,75,47],
        38 => [13,74,46,32,75,47], 39 => [40,75,47,7,76,48], 40 => [18,75,47,31,76,48],
    ];

    private array $modules = [];
    private int $typeNumber = 1;
    private int $moduleCount = 21;
    private array $dataCache = [];
    private array $expTable = [];
    private array $logTable = [];

    public static function pngDataUri(string $text, int $scale = 4, int $quietZone = 4): string
    {
        return (new self())->renderPngDataUri($text, $scale, $quietZone);
    }

    public function renderPngDataUri(string $text, int $scale = 4, int $quietZone = 4): string
    {
        $matrix = $this->make($text);
        $matrixSize = count($matrix);
        $imageSize = ($matrixSize + ($quietZone * 2)) * $scale;
        $image = imagecreatetruecolor($imageSize, $imageSize);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);

        imagefill($image, 0, 0, $white);

        foreach ($matrix as $row => $columns) {
            foreach ($columns as $column => $dark) {
                if (!$dark) {
                    continue;
                }

                imagefilledrectangle(
                    $image,
                    ($column + $quietZone) * $scale,
                    ($row + $quietZone) * $scale,
                    (($column + $quietZone + 1) * $scale) - 1,
                    (($row + $quietZone + 1) * $scale) - 1,
                    $black
                );
            }
        }

        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);

        return 'data:image/png;base64,' . base64_encode((string) $png);
    }

    public function make(string $text): array
    {
        $bytes = $this->stringToBytes($text);
        $this->typeNumber = $this->chooseTypeNumber($bytes);
        $this->moduleCount = $this->typeNumber * 4 + 17;
        $this->dataCache = $this->createData($bytes);

        $bestMask = $this->getBestMaskPattern();
        $this->makeImpl(false, $bestMask);

        return $this->modules;
    }

    private function chooseTypeNumber(array $bytes): int
    {
        for ($type = 1; $type <= 40; $type++) {
            $totalDataCount = $this->totalDataCount($type);
            $lengthBits = $this->lengthInBits($type);
            $requiredBits = 4 + $lengthBits + (count($bytes) * 8);

            if ($requiredBits <= $totalDataCount * 8) {
                return $type;
            }
        }

        throw new InvalidArgumentException('O conteúdo do QR Code excede o limite suportado.');
    }

    private function makeImpl(bool $test, int $maskPattern): void
    {
        $this->modules = array_fill(0, $this->moduleCount, array_fill(0, $this->moduleCount, null));

        $this->setupPositionProbePattern(0, 0);
        $this->setupPositionProbePattern($this->moduleCount - 7, 0);
        $this->setupPositionProbePattern(0, $this->moduleCount - 7);
        $this->setupPositionAdjustPattern();
        $this->setupTimingPattern();
        $this->setupTypeInfo($test, $maskPattern);

        if ($this->typeNumber >= 7) {
            $this->setupTypeNumber($test);
        }

        $this->mapData($this->dataCache, $maskPattern);
    }

    private function setupPositionProbePattern(int $row, int $col): void
    {
        for ($r = -1; $r <= 7; $r++) {
            if ($row + $r <= -1 || $this->moduleCount <= $row + $r) {
                continue;
            }

            for ($c = -1; $c <= 7; $c++) {
                if ($col + $c <= -1 || $this->moduleCount <= $col + $c) {
                    continue;
                }

                $this->modules[$row + $r][$col + $c] = (0 <= $r && $r <= 6 && ($c === 0 || $c === 6))
                    || (0 <= $c && $c <= 6 && ($r === 0 || $r === 6))
                    || (2 <= $r && $r <= 4 && 2 <= $c && $c <= 4);
            }
        }
    }

    private function setupTimingPattern(): void
    {
        for ($r = 8; $r < $this->moduleCount - 8; $r++) {
            if ($this->modules[$r][6] !== null) {
                continue;
            }
            $this->modules[$r][6] = ($r % 2 === 0);
        }

        for ($c = 8; $c < $this->moduleCount - 8; $c++) {
            if ($this->modules[6][$c] !== null) {
                continue;
            }
            $this->modules[6][$c] = ($c % 2 === 0);
        }
    }

    private function setupPositionAdjustPattern(): void
    {
        $positions = self::PATTERN_POSITION_TABLE[$this->typeNumber - 1];

        foreach ($positions as $row) {
            foreach ($positions as $col) {
                if ($this->modules[$row][$col] !== null) {
                    continue;
                }

                for ($r = -2; $r <= 2; $r++) {
                    for ($c = -2; $c <= 2; $c++) {
                        $this->modules[$row + $r][$col + $c] = $r === -2 || $r === 2 || $c === -2 || $c === 2 || ($r === 0 && $c === 0);
                    }
                }
            }
        }
    }

    private function setupTypeNumber(bool $test): void
    {
        $bits = $this->getBchTypeNumber($this->typeNumber);

        for ($i = 0; $i < 18; $i++) {
            $mod = !$test && (($bits >> $i) & 1) === 1;
            $this->modules[intdiv($i, 3)][$i % 3 + $this->moduleCount - 8 - 3] = $mod;
        }

        for ($i = 0; $i < 18; $i++) {
            $mod = !$test && (($bits >> $i) & 1) === 1;
            $this->modules[$i % 3 + $this->moduleCount - 8 - 3][intdiv($i, 3)] = $mod;
        }
    }

    private function setupTypeInfo(bool $test, int $maskPattern): void
    {
        $data = (self::ERROR_CORRECTION_M << 3) | $maskPattern;
        $bits = $this->getBchTypeInfo($data);

        for ($i = 0; $i < 15; $i++) {
            $mod = !$test && (($bits >> $i) & 1) === 1;

            if ($i < 6) {
                $this->modules[$i][8] = $mod;
            } elseif ($i < 8) {
                $this->modules[$i + 1][8] = $mod;
            } else {
                $this->modules[$this->moduleCount - 15 + $i][8] = $mod;
            }
        }

        for ($i = 0; $i < 15; $i++) {
            $mod = !$test && (($bits >> $i) & 1) === 1;

            if ($i < 8) {
                $this->modules[8][$this->moduleCount - $i - 1] = $mod;
            } elseif ($i < 9) {
                $this->modules[8][15 - $i] = $mod;
            } else {
                $this->modules[8][15 - $i - 1] = $mod;
            }
        }

        $this->modules[$this->moduleCount - 8][8] = !$test;
    }

    private function mapData(array $data, int $maskPattern): void
    {
        $inc = -1;
        $row = $this->moduleCount - 1;
        $bitIndex = 7;
        $byteIndex = 0;

        for ($col = $this->moduleCount - 1; $col > 0; $col -= 2) {
            if ($col === 6) {
                $col--;
            }

            while (true) {
                for ($c = 0; $c < 2; $c++) {
                    if ($this->modules[$row][$col - $c] === null) {
                        $dark = false;

                        if ($byteIndex < count($data)) {
                            $dark = (($data[$byteIndex] >> $bitIndex) & 1) === 1;
                        }

                        if ($this->mask($maskPattern, $row, $col - $c)) {
                            $dark = !$dark;
                        }

                        $this->modules[$row][$col - $c] = $dark;
                        $bitIndex--;

                        if ($bitIndex === -1) {
                            $byteIndex++;
                            $bitIndex = 7;
                        }
                    }
                }

                $row += $inc;

                if ($row < 0 || $this->moduleCount <= $row) {
                    $row -= $inc;
                    $inc = -$inc;
                    break;
                }
            }
        }
    }

    private function createData(array $bytes): array
    {
        $rsBlocks = $this->rsBlocks($this->typeNumber);
        $buffer = [];
        $this->put($buffer, self::MODE_8BIT_BYTE, 4);
        $this->put($buffer, count($bytes), $this->lengthInBits($this->typeNumber));

        foreach ($bytes as $byte) {
            $this->put($buffer, $byte, 8);
        }

        $totalDataCount = $this->totalDataCount($this->typeNumber);

        if (count($buffer) > $totalDataCount * 8) {
            throw new InvalidArgumentException('O conteúdo do QR Code excede o limite suportado.');
        }

        if (count($buffer) + 4 <= $totalDataCount * 8) {
            $this->put($buffer, 0, 4);
        }

        while (count($buffer) % 8 !== 0) {
            $buffer[] = false;
        }

        while (true) {
            if (count($buffer) >= $totalDataCount * 8) {
                break;
            }
            $this->put($buffer, 0xEC, 8);

            if (count($buffer) >= $totalDataCount * 8) {
                break;
            }
            $this->put($buffer, 0x11, 8);
        }

        return $this->createBytes($buffer, $rsBlocks);
    }

    private function createBytes(array $buffer, array $rsBlocks): array
    {
        $bytes = [];
        foreach (array_chunk($buffer, 8) as $chunk) {
            $value = 0;
            foreach ($chunk as $bit) {
                $value = ($value << 1) | ($bit ? 1 : 0);
            }
            $bytes[] = $value;
        }

        $offset = 0;
        $maxDcCount = 0;
        $maxEcCount = 0;
        $dcdata = [];
        $ecdata = [];

        foreach ($rsBlocks as $r => $block) {
            $dcCount = $block['dataCount'];
            $ecCount = $block['totalCount'] - $dcCount;
            $maxDcCount = max($maxDcCount, $dcCount);
            $maxEcCount = max($maxEcCount, $ecCount);
            $dcdata[$r] = array_slice($bytes, $offset, $dcCount);
            $offset += $dcCount;

            $rsPoly = $this->errorCorrectPolynomial($ecCount);
            $rawPoly = $this->polynomial($dcdata[$r], count($rsPoly) - 1);
            $modPoly = $this->polynomialMod($rawPoly, $rsPoly);
            $ecdata[$r] = array_fill(0, count($rsPoly) - 1, 0);

            for ($i = 0; $i < count($ecdata[$r]); $i++) {
                $modIndex = $i + count($modPoly) - count($ecdata[$r]);
                $ecdata[$r][$i] = $modIndex >= 0 ? $modPoly[$modIndex] : 0;
            }
        }

        $data = [];
        for ($i = 0; $i < $maxDcCount; $i++) {
            foreach ($dcdata as $block) {
                if ($i < count($block)) {
                    $data[] = $block[$i];
                }
            }
        }

        for ($i = 0; $i < $maxEcCount; $i++) {
            foreach ($ecdata as $block) {
                if ($i < count($block)) {
                    $data[] = $block[$i];
                }
            }
        }

        return $data;
    }

    private function put(array &$buffer, int $num, int $length): void
    {
        for ($i = 0; $i < $length; $i++) {
            $buffer[] = (($num >> ($length - $i - 1)) & 1) === 1;
        }
    }

    private function getBestMaskPattern(): int
    {
        $minLostPoint = 0;
        $pattern = 0;

        for ($i = 0; $i < 8; $i++) {
            $this->makeImpl(true, $i);
            $lostPoint = $this->lostPoint();

            if ($i === 0 || $minLostPoint > $lostPoint) {
                $minLostPoint = $lostPoint;
                $pattern = $i;
            }
        }

        return $pattern;
    }

    private function lostPoint(): float
    {
        $lostPoint = 0;

        for ($row = 0; $row < $this->moduleCount; $row++) {
            for ($col = 0; $col < $this->moduleCount; $col++) {
                $sameCount = 0;
                $dark = $this->isDark($row, $col);

                for ($r = -1; $r <= 1; $r++) {
                    if ($row + $r < 0 || $this->moduleCount <= $row + $r) {
                        continue;
                    }

                    for ($c = -1; $c <= 1; $c++) {
                        if ($col + $c < 0 || $this->moduleCount <= $col + $c || ($r === 0 && $c === 0)) {
                            continue;
                        }

                        if ($dark === $this->isDark($row + $r, $col + $c)) {
                            $sameCount++;
                        }
                    }
                }

                if ($sameCount > 5) {
                    $lostPoint += 3 + $sameCount - 5;
                }
            }
        }

        for ($row = 0; $row < $this->moduleCount - 1; $row++) {
            for ($col = 0; $col < $this->moduleCount - 1; $col++) {
                $count = 0;
                if ($this->isDark($row, $col)) $count++;
                if ($this->isDark($row + 1, $col)) $count++;
                if ($this->isDark($row, $col + 1)) $count++;
                if ($this->isDark($row + 1, $col + 1)) $count++;
                if ($count === 0 || $count === 4) {
                    $lostPoint += 3;
                }
            }
        }

        for ($row = 0; $row < $this->moduleCount; $row++) {
            for ($col = 0; $col < $this->moduleCount - 6; $col++) {
                if ($this->isDark($row, $col)
                    && !$this->isDark($row, $col + 1)
                    && $this->isDark($row, $col + 2)
                    && $this->isDark($row, $col + 3)
                    && $this->isDark($row, $col + 4)
                    && !$this->isDark($row, $col + 5)
                    && $this->isDark($row, $col + 6)) {
                    $lostPoint += 40;
                }
            }
        }

        for ($col = 0; $col < $this->moduleCount; $col++) {
            for ($row = 0; $row < $this->moduleCount - 6; $row++) {
                if ($this->isDark($row, $col)
                    && !$this->isDark($row + 1, $col)
                    && $this->isDark($row + 2, $col)
                    && $this->isDark($row + 3, $col)
                    && $this->isDark($row + 4, $col)
                    && !$this->isDark($row + 5, $col)
                    && $this->isDark($row + 6, $col)) {
                    $lostPoint += 40;
                }
            }
        }

        $darkCount = 0;
        for ($col = 0; $col < $this->moduleCount; $col++) {
            for ($row = 0; $row < $this->moduleCount; $row++) {
                if ($this->isDark($row, $col)) {
                    $darkCount++;
                }
            }
        }

        $ratio = abs(100 * $darkCount / $this->moduleCount / $this->moduleCount - 50) / 5;
        $lostPoint += $ratio * 10;

        return $lostPoint;
    }

    private function rsBlocks(int $typeNumber): array
    {
        $row = self::RS_BLOCKS_M[$typeNumber] ?? null;
        if ($row === null) {
            throw new InvalidArgumentException('Tipo de QR Code inválido.');
        }

        $blocks = [];
        for ($i = 0; $i < count($row); $i += 3) {
            for ($j = 0; $j < $row[$i]; $j++) {
                $blocks[] = ['totalCount' => $row[$i + 1], 'dataCount' => $row[$i + 2]];
            }
        }

        return $blocks;
    }

    private function totalDataCount(int $typeNumber): int
    {
        return array_sum(array_map(fn ($block) => $block['dataCount'], $this->rsBlocks($typeNumber)));
    }

    private function lengthInBits(int $typeNumber): int
    {
        return $typeNumber < 10 ? 8 : 16;
    }

    private function errorCorrectPolynomial(int $errorCorrectLength): array
    {
        $a = [1];
        for ($i = 0; $i < $errorCorrectLength; $i++) {
            $a = $this->polynomialMultiply($a, [1, $this->gexp($i)]);
        }

        return $a;
    }

    private function polynomial(array $num, int $shift): array
    {
        $offset = 0;
        while ($offset < count($num) && $num[$offset] === 0) {
            $offset++;
        }

        return array_merge(array_slice($num, $offset), array_fill(0, $shift, 0));
    }

    private function polynomialMultiply(array $a, array $b): array
    {
        $num = array_fill(0, count($a) + count($b) - 1, 0);

        for ($i = 0; $i < count($a); $i++) {
            for ($j = 0; $j < count($b); $j++) {
                $num[$i + $j] ^= $this->gexp($this->glog($a[$i]) + $this->glog($b[$j]));
            }
        }

        return $this->polynomial($num, 0);
    }

    private function polynomialMod(array $a, array $b): array
    {
        $a = $this->polynomial($a, 0);
        if (count($a) < count($b)) {
            return $a;
        }

        $ratio = $this->glog($a[0]) - $this->glog($b[0]);
        $num = $a;

        for ($i = 0; $i < count($b); $i++) {
            $num[$i] ^= $this->gexp($this->glog($b[$i]) + $ratio);
        }

        return $this->polynomialMod($num, $b);
    }

    private function gexp(int $n): int
    {
        $this->initMath();
        while ($n < 0) {
            $n += 255;
        }
        while ($n >= 256) {
            $n -= 255;
        }

        return $this->expTable[$n];
    }

    private function glog(int $n): int
    {
        $this->initMath();
        if ($n < 1) {
            throw new InvalidArgumentException('glog inválido.');
        }

        return $this->logTable[$n];
    }

    private function initMath(): void
    {
        if ($this->expTable && $this->logTable) {
            return;
        }

        for ($i = 0; $i < 8; $i++) {
            $this->expTable[$i] = 1 << $i;
        }

        for ($i = 8; $i < 256; $i++) {
            $this->expTable[$i] = $this->expTable[$i - 4]
                ^ $this->expTable[$i - 5]
                ^ $this->expTable[$i - 6]
                ^ $this->expTable[$i - 8];
        }

        for ($i = 0; $i < 255; $i++) {
            $this->logTable[$this->expTable[$i]] = $i;
        }
    }

    private function getBchTypeInfo(int $data): int
    {
        $d = $data << 10;
        while ($this->bchDigit($d) - $this->bchDigit(self::G15) >= 0) {
            $d ^= self::G15 << ($this->bchDigit($d) - $this->bchDigit(self::G15));
        }

        return (($data << 10) | $d) ^ self::G15_MASK;
    }

    private function getBchTypeNumber(int $data): int
    {
        $d = $data << 12;
        while ($this->bchDigit($d) - $this->bchDigit(self::G18) >= 0) {
            $d ^= self::G18 << ($this->bchDigit($d) - $this->bchDigit(self::G18));
        }

        return ($data << 12) | $d;
    }

    private function bchDigit(int $data): int
    {
        $digit = 0;
        while ($data !== 0) {
            $digit++;
            $data >>= 1;
        }

        return $digit;
    }

    private function mask(int $maskPattern, int $i, int $j): bool
    {
        return match ($maskPattern) {
            0 => ($i + $j) % 2 === 0,
            1 => $i % 2 === 0,
            2 => $j % 3 === 0,
            3 => ($i + $j) % 3 === 0,
            4 => (intdiv($i, 2) + intdiv($j, 3)) % 2 === 0,
            5 => ($i * $j) % 2 + ($i * $j) % 3 === 0,
            6 => (($i * $j) % 2 + ($i * $j) % 3) % 2 === 0,
            7 => (($i * $j) % 3 + ($i + $j) % 2) % 2 === 0,
            default => throw new InvalidArgumentException('Máscara de QR Code inválida.'),
        };
    }

    private function isDark(int $row, int $col): bool
    {
        return (bool) $this->modules[$row][$col];
    }

    private function stringToBytes(string $text): array
    {
        $bytes = [];
        $length = strlen($text);
        for ($i = 0; $i < $length; $i++) {
            $bytes[] = ord($text[$i]) & 0xff;
        }

        return $bytes;
    }
}
