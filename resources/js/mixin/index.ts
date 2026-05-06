export const formatDMY = (
    date: string | Date | null | undefined,
    withTime: boolean = false
): string => {
    if (!date) return '';

    const d = new Date(date);

    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();

    if (!withTime) {
        return `${day}-${month}-${year}`;
    }

    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');

    return `${day}-${month}-${year} ${hours}:${minutes}`;
};

export const formatTime = (
    date: string | Date | null | undefined,
    use12Hour: boolean = false // false = 24-hour (default), true = 12-hour with AM/PM
): string => {
    if (!date) return '';

    const d = new Date(date);

    if (use12Hour) {
        let hours = d.getHours();
        const minutes = String(d.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12;
        hours = hours ? hours : 12; // Convert 0 to 12
        const formattedHours = String(hours).padStart(2, '0');

        return `${formattedHours}:${minutes} ${ampm}`;
    } else {
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${hours}:${minutes}`;
    }
};

export function formatNumber(value, decimals = 2) {
    if (value === null || value === undefined || value === '') return '';

    const num = Number(value.toString().replace(/,/g, ''));
    if (isNaN(num)) return value;


    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(num);
}

export const formatCnic = (value: string) => {
    if (!value) return '';
    let cnic = value.replace(/[^0-9]/g, '');   // insert only numbers, remove letters.spaces
    if (cnic.length > 5 && cnic.length <= 12) {
        cnic = cnic.slice(0, 5) + '-' + cnic.slice(5);
    } else if (cnic.length > 12) {
        cnic = cnic.slice(0, 5) + '-' + cnic.slice(5, 12) + '-' + cnic.slice(12, 13);
    }
    return cnic;
};


export function formatMobile(value) {
    if (!value) return '';

    let digits = value.replace(/\D/g, '');

    // limit to 11 digits
    digits = digits.substring(0, 11);

    // add dash after 4 digits
    if (digits.length > 4) {
        digits = digits.substring(0, 4) + '-' + digits.substring(4);
    }
    // console.log('output', value);
    return digits;
}

export function base64ToFile(base64, fileName) {
    const arr = base64.split(',');
    const mime = arr[0].match(/:(.*?);/)[1];
    const byteString = atob(arr[1]);

    let n = byteString.length;
    const u8arr = new Uint8Array(n);

    while (n--) {
        u8arr[n] = byteString.charCodeAt(n);
    }

    return new File([u8arr], fileName, { type: mime });
}

// You can export more global functions here
// export const formatCurrency = (value: number) => { ... }

// Add these functions after your existing imports
export function getDistance(p1: { x: number; y: number; }, p2: { x: number; y: number; }) {
    return Math.sqrt(Math.pow(p2.x - p1.x, 2) + Math.pow(p2.y - p1.y, 2));
}

const getMidpoint = (p1, p2) => {
    return {
        x: (p1.x + p2.x) / 2,
        y: (p1.y + p2.y) / 2
    };
};

// Smooth drawing using cubic Bézier curves
export function drawSmoothCurve(ctx: {
    beginPath: () => void;
    moveTo: (arg0: any, arg1: any) => void;
    bezierCurveTo: (arg0: any, arg1: any, arg2: any, arg3: any, arg4: any, arg5: any) => void;
    stroke: () => void;
}, points: string | any[]) {
    if (points.length < 2) return;

    ctx.beginPath();
    ctx.moveTo(points[0].x, points[0].y);

    for (let i = 1; i < points.length - 1; i++) {
        const current = points[i];
        const next = points[i + 1];

        // Calculate control points for smoother curve
        const cp1 = {
            x: current.x + (next.x - current.x) * 0.25,
            y: current.y + (next.y - current.y) * 0.25
        };

        const cp2 = {
            x: current.x + (next.x - current.x) * 0.75,
            y: current.y + (next.y - current.y) * 0.75
        };

        ctx.bezierCurveTo(cp1.x, cp1.y, cp2.x, cp2.y, next.x, next.y);
    }

    ctx.stroke();
}

// Alternative: Quadratic Bézier with adaptive sampling
export function drawQuadraticBezierCurve(ctx: {
    beginPath: () => void;
    moveTo: (arg0: any, arg1: any) => void;
    quadraticCurveTo: (arg0: any, arg1: any, arg2: any, arg3: any) => void;
    lineTo: (arg0: any, arg1: any) => void;
    stroke: () => void;
}, points: string | any[]) {
    if (points.length < 2) return;

    ctx.beginPath();
    ctx.moveTo(points[0].x, points[0].y);

    for (let i = 0; i < points.length - 2; i++) {
        const p0 = points[i];
        const p1 = points[i + 1];
        const p2 = points[i + 2];

        // Calculate control point for quadratic curve
        const cp = {
            x: p1.x,
            y: p1.y
        };

        ctx.quadraticCurveTo(cp.x, cp.y, p2.x, p2.y);
    }

    // Handle last segment
    if (points.length >= 2) {
        const last = points[points.length - 1];
        const secondLast = points[points.length - 2];
        ctx.lineTo(last.x, last.y);
    }

    ctx.stroke();
}

// Advanced: Catmull-Rom to Bézier conversion (smoothest)
export function catmullRomToBezier(points: string | any[]) {
    const bezierPoints: any = [];

    for (let i = 0; i < points.length - 1; i++) {
        const p0 = points[Math.max(0, i - 1)];
        const p1 = points[i];
        const p2 = points[i + 1];
        const p3 = points[Math.min(points.length - 1, i + 2)];

        // Calculate control points
        const cp1 = {
            x: p1.x + (p2.x - p0.x) / 6,
            y: p1.y + (p2.y - p0.y) / 6
        };

        const cp2 = {
            x: p2.x - (p3.x - p1.x) / 6,
            y: p2.y - (p3.y - p1.y) / 6
        };

        bezierPoints.push({ point: p1, cp1, cp2, end: p2 });
    }

    return bezierPoints;
}

export function drawCatmullRomCurve(ctx: {
    beginPath: () => void;
    moveTo: (arg0: any, arg1: any) => void;
    bezierCurveTo: (arg0: any, arg1: any, arg2: any, arg3: any, arg4: any, arg5: any) => void;
    stroke: () => void;
}, points: string | any[]) {
    if (points.length < 2) return;

    const bezierSegments = catmullRomToBezier(points);

    ctx.beginPath();
    ctx.moveTo(points[0].x, points[0].y);

    for (const segment of bezierSegments) {
        // @ts-ignore
        ctx.bezierCurveTo(
            segment.cp1.x, segment.cp1.y,
            segment.cp2.x, segment.cp2.y,
            segment.end.x, segment.end.y
        );
    }

    ctx.stroke();
}
