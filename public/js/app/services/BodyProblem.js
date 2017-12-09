define([
], function () {
    /* 
    The acceleration equations for the 2D three-body problem (see equations 18 through 21):

    d^2[x1]/dt^2 = G*m2*(x2 - x1) / alpha

    d^2[y1]/dt^2 = G*m2*(y2 - y1) / alpha

    d^2[x2]/dt^2 = G*m1*(x1 - x2) / alpha

    d^2[y2]/dt^2 = G*m1*(y1 - y2) / alpha

    where: 

    G = gravitational constant = 6.6725985 X 10^(-11) N-m^2/kg^2

    alpha = [ (x2 - x1)^2 + (y2 - y1)^2 ]^(3/2)  AND  alpha <> 0 (if alpha = 0, we have an infinite acceleration which is not physically possible)
    */
    function Helper() {
        var self = this;
        var G = 6.67384E-11; // Big-G, in N(m/kg)^2.
        var h = 0.00001; // Interval between time steps, in seconds. The smaller the value the more accurate the simulation. This value was empirically derived by visually observing the simulation over time.
        var iterationsPerFrame = 100; // The number of calculations made per animation frame, this is an empirically derived number based on the value of h.

        var bodies = [];
        var half = [];
        
        function alpha(m1, m2) {
            var delta_x = m2.p.x - m1.p.x;
            var delta_y = m2.p.y - m1.p.y;
            var delta_z = m2.p.z - m1.p.z;

            var delta_x_squared = delta_x * delta_x;
            var delta_y_squared = delta_y * delta_y;
            var delta_z_squared = delta_z * delta_z;

            var base = delta_x_squared + delta_y_squared + delta_z_squared;

            return Math.sqrt(base * base * base);
        }
        function equation23(v, a, h) {
            return v + h * a;
        }

        function equation24(x, v, h) {
            return x + 0.5 * h * v;
        }

        function equation22(x, v, h) {
            return x + 0.5 * h * v;
        }
        function equation25(x, v, a, h) {
            return x + 0.5 * h * v + 0.25 * (h * h) * a;
        }
        this.getTrajectory = function(zoom){
            var points = [];
            var bodiesClone = bodies;
            var halfClone = half;
            var ends= {};
            var iteration = 0; 
            var speed = 100;
            while(Object.keys(ends).length < bodiesClone.length){
                iteration++;
                for (var i in halfClone) {
                    var x = 0, y = 0, z = 0;
                    for (var j in halfClone) {
                        if (j !== i) {
                            x += G * halfClone[j].m * (halfClone[j].p.x - halfClone[i].p.x) / alpha(halfClone[i], halfClone[j]);
                            y += G * halfClone[j].m * (halfClone[j].p.y - halfClone[i].p.y) / alpha(halfClone[i], halfClone[j]);
                            z += G * halfClone[j].m * (halfClone[j].p.z - halfClone[i].p.z) / alpha(halfClone[i], halfClone[j]);
                            break;
                        }
                    }
                    halfClone[i].a.x = x;
                    halfClone[i].a.y = y;
                    halfClone[i].a.z = z;
                }
                for (var i in bodiesClone) {
                    
                    bodiesClone[i].v.x = equation23(bodiesClone[i].v.x, halfClone[i].a.x, h*speed);
                    bodiesClone[i].v.y = equation23(bodiesClone[i].v.y, halfClone[i].a.y, h*speed);
                    bodiesClone[i].v.z = equation23(bodiesClone[i].v.z, halfClone[i].a.z, h*speed);

                    bodiesClone[i].p.x = equation24(half[i].p.x, bodiesClone[i].v.x, h*speed);
                    bodiesClone[i].p.y = equation24(half[i].p.y, bodiesClone[i].v.y, h*speed);
                    bodiesClone[i].p.z = equation24(half[i].p.z, bodiesClone[i].v.z, h*speed);

                    var prevX = 0;
                    var prevY = 0;
                        
                    var valX = Math.ceil((bodiesClone[i].p.x/zoom) * 10)/10;
                    var valY = Math.ceil((bodiesClone[i].p.y/zoom) * 10)/10;
                    
                    if(points[i] === undefined){
                        points[i] = [];
                    }else{
                        prevX = points[i][points[i].length-2];
                        prevY = points[i][points[i].length-1];
                    }
                    if(points[i].length > 0 && points[i][0]==valX && points[i][1]==valY){
                        ends[i] = true;
                    }
                    if(ends[i] === undefined){
                        if(prevX!=valX
                            || prevY!=valY
                        ){
                            points[i][points[i].length] = valX;
                            points[i][points[i].length] = valY;
                        }
                    }
                    
                    halfClone[i].p.x = equation22(bodiesClone[i].p.x, bodiesClone[i].v.x, h*speed);
                    halfClone[i].p.y = equation22(bodiesClone[i].p.y, bodiesClone[i].v.y, h*speed);
                    halfClone[i].p.z = equation22(bodiesClone[i].p.z, bodiesClone[i].v.z, h*speed);
                }
            }
            console.log('iteration', iteration);
            return points;
        };
        this.init = function (initialConditions, callback) {
            this.callback = callback;
            function Mass(initialCondition) {
                this.m = initialCondition.mass; 
                this.p = {x: initialCondition.position.x, y: initialCondition.position.y, z: initialCondition.position.z};
                this.v = {x: initialCondition.velocity.x, y: initialCondition.velocity.y, z: initialCondition.velocity.z};
                this.a = {};
            }

            for(var i in initialConditions){
                bodies[i] = new Mass(initialConditions[i]);
                half[i] =  new Mass(initialConditions[i]);
            }
            for(var i in bodies){
                var x = 0, y = 0, z = 0;
                for(var j in bodies){
                    if(j!==i){
                        x+= G * bodies[j].m * (bodies[j].p.x - bodies[i].p.x) / alpha(bodies[i], bodies[j]);
                        y+= G * bodies[j].m * (bodies[j].p.y - bodies[i].p.y) / alpha(bodies[i], bodies[j]);
                        z+= G * bodies[j].m * (bodies[j].p.z - bodies[i].p.z) / alpha(bodies[i], bodies[j]);
                        break;
                    }
                }
                bodies[i].a.x = x;
                bodies[i].a.y = y;
                bodies[i].a.z = z;
            }
            
            for(var i in half){
                half[i].p.x = equation25(bodies[i].p.x, bodies[i].v.x, bodies[i].a.x, h);
                half[i].p.y = equation25(bodies[i].p.y, bodies[i].v.y, bodies[i].a.y, h);
                half[i].p.z = equation25(bodies[i].p.z, bodies[i].v.z, bodies[i].a.z, h);
            }
        };

        this.crunch = function () {
            for (var iteration = 0; iteration < iterationsPerFrame; iteration++) {
                for(var i in half){
                    var x = 0, y = 0, z=0;
                    for(var j in half){
                        if(j!==i){
                            x+= G * half[j].m * (half[j].p.x - half[i].p.x) / alpha(half[i], half[j]);
                            y+= G * half[j].m * (half[j].p.y - half[i].p.y) / alpha(half[i], half[j]);
                            z+= G * half[j].m * (half[j].p.z - half[i].p.z) / alpha(half[i], half[j]);
                        }
                    }
                    half[i].a.x = x;
                    half[i].a.y = y;
                    half[i].a.z = z;
                }
                for(var i in bodies){
                    bodies[i].v.x = equation23(bodies[i].v.x, half[i].a.x, h);
                    bodies[i].v.y = equation23(bodies[i].v.y, half[i].a.y, h);
                    bodies[i].v.z = equation23(bodies[i].v.z, half[i].a.z, h);
                    
                    bodies[i].p.x = equation24(half[i].p.x, bodies[i].v.x, h);
                    bodies[i].p.y = equation24(half[i].p.y, bodies[i].v.y, h);
                    bodies[i].p.z = equation24(half[i].p.z, bodies[i].v.z, h);
                    
                    half[i].p.x = equation22(bodies[i].p.x, bodies[i].v.x, h);
                    half[i].p.y = equation22(bodies[i].p.y, bodies[i].v.y, h);
                    half[i].p.z = equation22(bodies[i].p.z, bodies[i].v.z, h);
                }
            }

            self.callback(bodies);
        };
    }
    return Helper;
});