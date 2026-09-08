<?php

namespace App\Enums;

enum LetterGrade: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';
    case D = 'D';
    case F = 'F';
    case IC = 'IC';
    case IP = 'IP';

    /**
     * Get 4.0 scale Grade Point
     */
    public function gradePoint(): float
    {
        return match ($this) {
            self::A => 4.0,
            self::B => 3.0,
            self::C => 2.0,
            self::D => 1.0,
            self::F, self::IC, self::IP => 0.0,
        };
    }

    /**
     * Get Azerbaijani label
     */
    public function labelAz(): string
    {
        return match ($this) {
            self::A => 'Əla (4.0)',
            self::B => 'Yaxşı (3.0)',
            self::C => 'Kafi (2.0)',
            self::D => 'Aşağı Keçid (1.0)',
            self::F => 'Qeyri-kafi (0.0)',
            self::IC => 'Yarımçıq (Incomplete)',
            self::IP => 'Davam edir (In Progress)',
        };
    }

    /**
     * Calculate letter grade from total score (0-100) and degree level
     */
    public static function fromScore(float $totalScore, string $degreeLevel = 'bachelor'): self
    {
        $degree = strtolower($degreeLevel);

        if ($degree === 'master') {
            // Master standard: F < 65
            if ($totalScore >= 90) {
                return self::A;
            }
            if ($totalScore >= 80) {
                return self::B;
            }
            if ($totalScore >= 70) {
                return self::C;
            }
            if ($totalScore >= 65) {
                return self::D;
            }

            return self::F;
        }

        if ($degree === 'phd') {
            // PhD standard: F < 70
            if ($totalScore >= 90) {
                return self::A;
            }
            if ($totalScore >= 80) {
                return self::B;
            }
            if ($totalScore >= 70) {
                return self::C;
            }

            return self::F;
        }

        // Standard Bachelor (Bakalavr) 5-tier scale
        if ($totalScore >= 90) {
            return self::A;
        }
        if ($totalScore >= 80) {
            return self::B;
        }
        if ($totalScore >= 70) {
            return self::C;
        }
        if ($totalScore >= 60) {
            return self::D;
        }

        return self::F;
    }

    /**
     * Determine if the grade earns course credits
     */
    public function earnsCredit(string $degreeLevel = 'bachelor'): bool
    {
        return $this !== self::F && $this !== self::IC && $this !== self::IP;
    }
}
