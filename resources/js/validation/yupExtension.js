import * as yup from 'yup';

const getImageDimensions = (source) => {
    return new Promise((resolve, reject) => {
        const img = new Image();

        img.onload = () => {
            if (source instanceof File && img.src.startsWith('blob:')) {
                URL.revokeObjectURL(img.src);
            }
            resolve({ width: img.width, height: img.height });
        };

        img.onerror = () => {
            if (source instanceof File && img.src.startsWith('blob:')) {
                URL.revokeObjectURL(img.src);
            }
            reject(new Error('تصویر لوڈ کرنے میں ناکامی'));
        };

        if (source instanceof File) {
            img.src = URL.createObjectURL(source);
        } else if (typeof source === 'string' && source.startsWith('data:image/')) {
            img.src = source;
        } else {
            reject(new Error('غلط تصویر کا منبع'));
        }
    });
};

/**
 * Add image dimension validation to Yup
 * @returns {yup.MixedSchema} Returns the schema for chaining
 */
yup.addMethod(yup.mixed, 'validateImage', function(config = {}) {
    const {
        minWidth,
        maxWidth,
        minHeight,
        maxHeight,
        exactWidth,
        exactHeight,
        minSize,
        maxSize,
        exactSize,
        aspectRatio,
        aspectRatioTolerance = 0.01,
        maxFileSizeMB = null,
        customMessage = null
    } = config;

    let chain = this;

    // Handle file size validation
    if (maxFileSizeMB) {
        chain = chain.test('file-size',
            `فائل کا سائز ${maxFileSizeMB}MB سے کم ہونا چاہیے`,
            (value) => {
                if (!value) return true;
                if (value instanceof File) {
                    return value.size <= maxFileSizeMB * 1024 * 1024;
                }
                if (typeof value === 'string' && value.startsWith('data:image/')) {
                    const base64Length = value.split(',')[1]?.length || value.length;
                    const sizeInBytes = Math.ceil(base64Length * 0.75);
                    return sizeInBytes <= maxFileSizeMB * 1024 * 1024;
                }
                return true;
            }
        );
    }

    // Apply shorthand constraints - FIX: Only use if explicitly provided
    const wMin = minSize !== undefined ? minSize : minWidth;
    const wMax = maxSize !== undefined ? maxSize : maxWidth;
    const hMin = minSize !== undefined ? minSize : minHeight;
    const hMax = maxSize !== undefined ? maxSize : maxHeight;
    const wExact = exactSize !== undefined ? exactSize : exactWidth;
    const hExact = exactSize !== undefined ? exactSize : exactHeight;

    // Check if any dimension validation is actually required - FIX: Check for undefined as well
    const hasDimensionValidation =
        (wMin !== undefined && wMin !== null) ||
        (wMax !== undefined && wMax !== null) ||
        (hMin !== undefined && hMin !== null) ||
        (hMax !== undefined && hMax !== null) ||
        (wExact !== undefined && wExact !== null) ||
        (hExact !== undefined && hExact !== null) ||
        (aspectRatio !== undefined && aspectRatio !== null);

    // Only add dimension validation if needed
    if (hasDimensionValidation) {
        chain = chain.test('image-dimensions', async function(value) {
            if (!value) return true;

            const { createError, path } = this;

            try {
                const { width, height } = await getImageDimensions(value);

                // Check exact width
                if (wExact !== undefined && wExact !== null && width !== wExact) {
                    return createError({
                        path,
                        message: customMessage || `چوڑائی ${wExact} پکسلز ہونی چاہیے (موجودہ: ${width} پکسلز)`
                    });
                }

                // Check exact height
                if (hExact !== undefined && hExact !== null && height !== hExact) {
                    return createError({
                        path,
                        message: customMessage || `اونچائی ${hExact} پکسلز ہونی چاہیے (موجودہ: ${height} پکسلز)`
                    });
                }

                // Check minimum width
                if (wMin !== undefined && wMin !== null && width < wMin) {
                    return createError({
                        path,
                        message: customMessage || `کم از کم چوڑائی ${wMin} پکسلز ہونی چاہیے (موجودہ: ${width} پکسلز)`
                    });
                }

                // Check maximum width
                if (wMax !== undefined && wMax !== null && width > wMax) {
                    return createError({
                        path,
                        message: customMessage || `زیادہ سے زیادہ چوڑائی ${wMax} پکسلز ہونی چاہیے (موجودہ: ${width} پکسلز)`
                    });
                }

                // Check minimum height
                if (hMin !== undefined && hMin !== null && height < hMin) {
                    return createError({
                        path,
                        message: customMessage || `کم از کم اونچائی ${hMin} پکسلز ہونی چاہیے (موجودہ: ${height} پکسلز)`
                    });
                }

                // Check maximum height
                if (hMax !== undefined && hMax !== null && height > hMax) {
                    return createError({
                        path,
                        message: customMessage || `زیادہ سے زیادہ اونچائی ${hMax} پکسلز ہونی چاہیے (موجودہ: ${height} پکسلز)`
                    });
                }

                // Check aspect ratio
                if (aspectRatio !== undefined && aspectRatio !== null) {
                    const ratio = width / height;
                    if (Math.abs(ratio - aspectRatio) > aspectRatioTolerance) {
                        return createError({
                            path,
                            message: customMessage || `تناسب ${aspectRatio}:1 ہونا چاہیے (موجودہ: ${ratio.toFixed(2)}:1)`
                        });
                    }
                }

                return true;
            } catch (error) {
                return createError({
                    path,
                    message: customMessage || 'غلط تصویر فائل ہے'
                });
            }
        });
    }

    return chain;
});

// Also add for string schema (base64)
yup.addMethod(yup.string, 'validateImage', function(config = {}) {
    return yup.mixed().validateImage.call(this, config);
});

export default yup;
